<?php

namespace App\Repositories;

use App\User;
use App\Models\Panel;
use App\Models\Image;
use App\Models\File;
use \Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Interfaces\FileRepositoryInterface;

class FileRepository implements FileRepositoryInterface
{

    public function storePanelFile(Panel $panel, UploadedFile $file, $fileCategoryId = null)
    {
        $originalFilename = $file->getClientOriginalName();
        $convertedFilename = $file->hashName();
        $savePath = $panel->save_path . DIRECTORY_SEPARATOR;
        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();

        try {
            $file->storeAs($savePath . 'attachments', $convertedFilename);
        } catch (\Exception $e) {
            error_log("Failed to save file {$originalFilename} to {$savePath} as {$convertedFilename}. \r\n Error in FileRepository.php");
            throw new \Exception("Failed to save the uploaded file");
        }

        $newFile = File::create([
            'panel_id'          =>  $panel->id,
            'original_filename' =>  $originalFilename,
            'filename'          =>  $convertedFilename,
            'file_category_id'  =>  $fileCategoryId,
            'type'              =>  'file',
            'mime_type'         =>  $mimeType,
            'file_size'         =>  $fileSize,
        ]);

        return File::find($newFile->id);
    }


    public function archiveAndRemove(File $file)
    {

        if ($file->type === "url") {
            $file->delete();
            return true;
        }

        $panel = $file->panel;

        try {
            Storage::move($panel->save_path . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . $file->filename, $panel->save_path . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'archived' . DIRECTORY_SEPARATOR . $file->filename);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            throw new \Exception("Failed to remove physical file.");
        }

        $file->is_archived = true;
        $file->save();
        $file->delete();

        return true;
    }
}
