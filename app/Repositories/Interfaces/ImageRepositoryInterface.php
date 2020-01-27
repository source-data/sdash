<?php

namespace App\Repositories\Interfaces;

use App\User;
use App\Models\Panel;
use App\Models\Image;
use App\Models\File;
use \Illuminate\Http\UploadedFile;

interface ImageRepositoryInterface {

    public function storePanelImage(Panel $panel, UploadedFile $file);


}