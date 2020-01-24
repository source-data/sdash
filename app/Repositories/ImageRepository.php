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

class ImageRepository
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
            'mime_type'             => $mimeType,
            'original_filename'     => $originalFilename,
            'filename'              => $convertedFilename,
            'preview_filename'      => $previewFilename,
            'panel_id'              => $panel->id
        ]);

        return $image;
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
        Storage::makeDirectory($panel->id . DIRECTORY_SEPARATOR . "attachments");
        Storage::makeDirectory($panel->id . DIRECTORY_SEPARATOR . "attachments". DIRECTORY_SEPARATOR . "archived");
        return (string)$panel->id;
    }

}
