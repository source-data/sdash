<?php

namespace App\Repositories\Interfaces;

use App\User;
use App\Models\Panel;
use App\Models\Image;
use App\Models\File;
use \Illuminate\Http\UploadedFile;

interface FileRepositoryInterface
{

    public function storePanelFile(Panel $panel, UploadedFile $file);
    public function archiveAndRemove(File $file);
    public function duplicatePanelFiles(Panel $oldPanel, Panel $newPanel);
}
