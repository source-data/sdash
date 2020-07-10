<?php

namespace App\Repositories;

use App\User;
use App\Models\Panel;
use App\Models\Image;
use \Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\PdfToImage\Pdf as PdfConverter;
use App\Repositories\PanelRepository as PanelQuery;
use Intervention\Image\Facades\Image as ImageService;
use App\Repositories\Interfaces\ImageRepositoryInterface;

class ImageRepository implements ImageRepositoryInterface
{

    protected $thumbnailCompressionFactor = 80;

    /**
     * Store uploaded image file, create thumbnail and create image record
     *
     * @param Panel $panel
     * @param UploadedFile $file
     * @return void
     */
    public function storePanelImage(Panel $panel, UploadedFile $file)
    {
        $originalFilename = $file->getClientOriginalName();
        $convertedFilename = $file->hashName();
        $imagePath = $file->storeAs($this->makePanelDirectoryTree($panel), $convertedFilename);
        $savePath = config("filesystems.disks.panels.root") . DIRECTORY_SEPARATOR . $panel->id . DIRECTORY_SEPARATOR;
        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();
        $previewFilename = $convertedFilename;

        if($mimeType === "application/pdf") {
            $convertedImage = new PdfConverter(config("filesystems.disks.panels.root") . DIRECTORY_SEPARATOR . $imagePath);
            $convertedImage->saveImage($savePath . $convertedFilename . '.jpeg');
            $previewFilename = $convertedFilename . '.jpeg';
            $imagePath = $panel->id . "/" . $previewFilename;
        }

        // see http://image.intervention.io/api/heighten
        $thumbnail = ImageService::make(config("filesystems.disks.panels.root") . DIRECTORY_SEPARATOR . $imagePath)->heighten(300, function ($constraint) {
            $constraint->upsize();
        });

        $thumbnail->save($savePath . 'thumbnails' . DIRECTORY_SEPARATOR . $previewFilename, $this->thumbnailCompressionFactor);

        $image = Image::create([
            'panel_id'              => $panel->id,
            'mime_type'             => $mimeType,
            'original_filename'     => $originalFilename,
            'filename'              => $convertedFilename,
            'preview_filename'      => $previewFilename,
            'file_size'             => $fileSize
        ]);

        $panel->increment('version');
        $panel->save();

        return $image;
    }

    public function archiveAndRemove(Image $file)
    {
        $panel = $file->panel;

        try {
            Storage::move($panel->save_path . DIRECTORY_SEPARATOR . $file->filename, $panel->save_path . DIRECTORY_SEPARATOR . 'archived' . DIRECTORY_SEPARATOR . $file->filename);
            Storage::delete($panel->save_path . DIRECTORY_SEPARATOR . 'thumbnails' . DIRECTORY_SEPARATOR . $file->filename);
        } catch(\Exception $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            throw new \Exception("Failed to remove physical file.");
        }

        $file->is_archived = true;
        $file->save();
        $file->delete();

        return true;
    }

    /**
     * Create directories for a new panel in the storage directory
     *
     * @param Panel $panel
     * @return void
     */
    protected function makePanelDirectoryTree(Panel $panel)
    {
        Storage::makeDirectory($panel->id);
        Storage::makeDirectory($panel->id . DIRECTORY_SEPARATOR . "thumbnails");
        Storage::makeDirectory($panel->id . DIRECTORY_SEPARATOR . "archived");
        Storage::makeDirectory($panel->id . DIRECTORY_SEPARATOR . "attachments");
        Storage::makeDirectory($panel->id . DIRECTORY_SEPARATOR . "attachments". DIRECTORY_SEPARATOR . "archived");
        return (string)$panel->id;
    }

}
