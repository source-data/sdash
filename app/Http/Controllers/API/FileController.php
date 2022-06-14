<?php

namespace App\Http\Controllers\API;

use API;
use App\User;
use App\Models\FileCategory;
use App\Models\File;
use App\Models\Panel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\FileRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Access\AuthorizationException;
use App\Repositories\Interfaces\FileRepositoryInterface;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{

    protected $fileRepository;

    public function __construct(FileRepositoryInterface $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Panel $panel, Request $request)
    {
        $user = auth()->user();

        $maxFileSizeInMB = 4;
        $maxFileSizeInBytes = 4 * 1000;
        $rules = [
            'file' => ['required', 'mimes:jpeg,png,jpg,gif,pdf,tif', "max:$maxFileSizeInBytes"],
        ];
        $messages = [
            'file.max' => "Source files may not be larger than $maxFileSizeInMB MB",
        ];
        $this->validate($request, $rules, $messages);

        if (Gate::allows('modify-panel', $panel)) {
            
            $fileCategoryId = (int) $request->input('file_category_id') ?: null;

            if ($request->input('url')) {
                $fileCreated = File::create([
                    'panel_id'          => $panel->id,
                    'url'               => $request->input('url'),
                    'file_category_id'  => $fileCategoryId,
                    'type'              => 'url'
                ]);

                return API::response(200, "External URL stored.", File::find($fileCreated->id));
            }

            if ($file = $request->file('file')) {

                return API::response(200, "File uploaded successfully", $this->fileRepository->storePanelFile($panel, $file, $fileCategoryId));
            }

            return API::response(400, "Malformed upload attempt - did you submit a file or url?", []);
        } else {
            abort(401, "Access denied.");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Files  $files
     * @return \Illuminate\Http\Response
     */
    public function show(Files $files)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Files  $files
     * @return \Illuminate\Http\Response
     */
    public function edit(Files $files)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Files  $files
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file)
    {
        $user = auth()->user();
        $panel = $file->panel;
        $request->validate([
            'file_category_id'  => ['integer', 'min:1', 'nullable'],
            'description'  => ['max:255', 'nullable']
        ]);

        if (!Gate::allows('modify-panel', $panel)) return API::response(401, "Access denied.", []);

        $file->update([
            'file_category_id' => $request->input("file_category_id"),
            'description' => strip_tags($request->input("description"))
        ]);

        return API::response(200, "File updated", $file);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Files  $files
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {

        $user = auth()->user();
        $panel = $file->panel;

        if (!Gate::allows('modify-panel', $panel)) return API::response(401, "Access denied.", []);

        if ($this->fileRepository->archiveAndRemove($file)) {
            return API::response(200, "File removed.", []);
        }

        return API::response(500, "Could not remove requested file", []);
    }

    public function download(File $file, Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            ['token' => ['string', 'exists:panel_access_tokens,token']]
        );

        if ($validator->fails()) abort(401, "Access Denied");

        $panel = $file->panel;
        $token = $request->get('token', null);

        if (!Gate::allows('view-single-panel', [$panel, $token])) throw new AuthorizationException("Access Denied");

        $fs = Storage::getDriver();

        $path = $panel->save_path . 'attachments' . DIRECTORY_SEPARATOR . $file->filename;
        $fileStream = $fs->readStream($path);

        return response()->stream(
            function () use ($fileStream) {
                while (ob_get_level() > 0) ob_end_flush();
                fpassthru($fileStream);
            },
            200,
            [
                'Content-Type'  =>  $file->mime_type,
                'Content-disposition' => 'attachment; filename="' . $file->original_filename . '"',
            ]
        );
    }

    public function listFileCategories()
    {
        return API::response(200, "List of file categories", FileCategory::all());
    }
}
