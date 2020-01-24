<?php

namespace App\Providers;

use App\Repositories\FileRepository;
use App\Repositories\Interfaces\FileRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            FileRepositoryInterface::class,
            FileRepository::class
        );
    }
}