<?php

namespace App\Http\Controllers;

use API;
use App\User;
use \Exception;
use App\Models\File;
use App\Models\Panel;
use App\Models\Image;
use App\Services\SDPDF;
use App\Services\DarXml;
use App\Services\ImageData;
use Illuminate\Http\Request;
use App\Services\DarManifest;
use App\Services\SDPowerpoint;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Services\MergeAndSortAuthors;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use ZipArchive;

class DownloadController extends Controller
{

    protected $token = null;

    public function __construct(Request $request)
    {
        // if token is submitted, check whether it passes validation
        $validator = Validator::make(
            $request->all(),
            ['token' => ['string', 'exists:panel_access_tokens,token']]
        );

        if ($validator->fails()) abort(401, "Access Denied");

        $this->token = $request->input("token");
    }

    public function downloadPdf(Panel $panel, Request $request)
    {

        if (!Gate::allows('view-single-panel', [$panel, $this->token])) abort(401, "Access denied");

        try {

            $imagePath = $this->getImageFilePath($panel);
            $authors = MergeAndSortAuthors::mergeAndSort($panel->authors->toArray(), $panel->externalAuthors->toArray());
            $authorString = (count($authors) === 0) ? null : $this->authorsToString($authors);

            $pdf = new SDPDF(($panel->title) ? $panel->title : "", ($panel->caption) ? $panel->caption : "", $imagePath, $authorString);

            $pdf->generateAndReturn();
        } catch (\Exception $e) {

            throw new \Exception("File cannot be downloaded", 500);
        }
    }

    public function downloadPowerpoint(Panel $panel, Request $request)
    {
        if (!Gate::allows('view-single-panel', [$panel, $this->token])) abort(401, "Access denied");

        try {

            $imagePath = $this->getImageFilePath($panel);

            $pdf = new SDPowerpoint(($panel->title) ? $panel->title : "", ($panel->caption) ? $panel->caption : "", $imagePath, $request->url());

            $pdf->generateAndReturn();
        } catch (\Exception $e) {

            throw new \Exception("File cannot be downloaded", 500);
        }
    }

    public function downloadOriginal(Panel $panel, Request $request)
    {
        if (!Gate::allows('view-single-panel', [$panel, $this->token])) abort(401, "Access denied");

        try {

            $panel->load('image');
            return Storage::download($panel->save_path . $panel->image->filename, $panel->image->original_filename);
        } catch (\Exception $e) {

            throw new \Exception("File cannot be downloaded", 500);
        }
    }

    public function downloadZip(Panel $panel, Request $request)
    {
        if (!Gate::allows('view-single-panel', [$panel, $this->token])) abort(401, "Access denied");

        try {
            $directoryPath = Storage::disk()->path($panel->save_path);

            $zipFileName = preg_replace('~[\\\\/:*?"<>|\s]~', '_', $panel->title) . '.zip';

            $zipContainer = $directoryPath . $zipFileName;

            if (file_exists($zipContainer)) unlink($zipContainer);

            $zip = new ZipArchive();

            $zip->open($zipContainer, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            $panel->load(['image', 'files' => function ($query) {
                $query->where('is_archived', false)->where('type', 'file');
            }]);

            $zip->addFile($this->getImageFilePath($panel), 'files/' . $panel->image->original_filename);

            if (count($panel->files) > 0) {
                foreach ($panel->files as $file) {
                    $zip->addFile(Storage::disk()->path($panel->save_path) . 'attachments' . DIRECTORY_SEPARATOR . $file->filename, 'files/' . $file->original_filename);
                }
            }

            $zip->close();

            $fs = Storage::getDriver();

            $fileStream = $fs->readStream($panel->save_path . $zipFileName);

            // chunked and streamed download to save server memory from excess load
            return response()->stream(
                function () use ($fileStream) {
                    while (ob_get_level() > 0) ob_end_flush();
                    fpassthru($fileStream);
                },
                200,
                [
                    'Content-Type'  =>  'application/zip',
                    'Content-disposition' => 'attachment; filename="' . $zipFileName . '"',
                    'pragma'        =>  'no-cache'
                ]
            );
        } catch (\Exception $e) {

            throw new \Exception("File cannot be downloaded", 500);
        }
    }


    public function downloadDar(Panel $panel, Request $request)
    {
        if (!Gate::allows('view-single-panel', [$panel, $this->token])) abort(401, "Access denied");

        try {

            $dar = new DarXml($panel->title);

            $manifest = new DarManifest();

            $directoryPath = Storage::disk()->path($panel->save_path);

            $zipFileName = preg_replace('~[\\\\/:*?"<>|\s]~', '_', $panel->title) . '.smartfigure';

            $zipContainer = $directoryPath . $zipFileName;

            if (file_exists($zipContainer)) unlink($zipContainer);

            $zip = new ZipArchive();
            $zip->open($zipContainer, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            $panel->load(['image', 'user', 'files' => function ($query) {
                $query->where('is_archived', false);
            }, 'tags' => function ($query) {
                $query->withPivot(['id', 'origin', 'role', 'type', 'category']);
            }]);

            $registeredAuthors = $panel->authors->toArray();
            $externalAuthors = $panel->externalAuthors->toArray();

            $sortedAuthors = MergeAndSortAuthors::mergeAndSort($registeredAuthors, $externalAuthors);

            foreach ($sortedAuthors as $auth) {
                $dar->appendAuthor($auth["firstname"], $auth["surname"], $auth["department_name"] . ', ' . $auth["institution_name"] . ', ' . $auth["institution_address"]);
            }

            $dar->appendPanel($panel);

            $zip->addFile($this->getImageFilePath($panel), $panel->image->original_filename);
            $manifest->appendAsset("panel-a", $panel->image->mime_type, $panel->image->original_filename);

            if (count($panel->files) > 0) {
                foreach ($panel->files as $file) {
                    if ($file->type == 'file') {
                        $zip->addFile(Storage::disk()->path($panel->save_path) . 'attachments' . DIRECTORY_SEPARATOR . $file->filename, $file->original_filename);
                        $manifest->appendAsset("file-" . $file->id, $file->mime_type, $file->original_filename);
                    }
                }
            }


            $zip->addFromString("smart-figure.xml", $dar->toString());
            $zip->addFromString("manifest.xml", $manifest->toString());

            $zip->close();

            $fs = Storage::getDriver();

            $fileStream = $fs->readStream($panel->save_path . $zipFileName);

            // chunked and streamed download to save server memory from excess load
            return response()->stream(
                function () use ($fileStream) {
                    while (ob_get_level() > 0) ob_end_flush();
                    fpassthru($fileStream);
                },
                200,
                [
                    'Content-Type'  =>  'application/dar',
                    'Content-disposition' => 'attachment; filename="' . $zipFileName . '"',
                    'pragma'        =>  'no-cache'
                ]
            );
        } catch (\Exception $e) {

            throw new \Exception("File cannot be downloaded", 500);
        }
    }

    /**
     * returns the path to the image file that will be embedded in one of the output streams
     *
     * @param Panel $panel
     * @return string
     */
    protected function getImageFilePath(Panel $panel)
    {
        $panel->load('image');
        return Storage::disk()->path($panel->save_path) . $panel->image->preview_filename;
    }


    protected function authorsToString(array $authors)
    {
        $authorCount = count($authors);

        if ($authorCount === 0) {
            return null;
        }

        $authorList = '';

        foreach ($authors as $author) {
            $authorList .= $author['firstname'] . ' ' . $author['surname'] . ', ';
        }

        return substr($authorList, 0, -2);
    }
}
