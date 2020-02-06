<?php

namespace App\Providers;

use App\Repositories\FileRepository;
use App\Repositories\ImageRepository;
use App\Repositories\PanelRepository;
use App\Repositories\GroupRepository;
use App\Repositories\Interfaces\FileRepositoryInterface;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Interfaces\PanelRepositoryInterface;
use App\Repositories\Interfaces\ImageRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            FileRepositoryInterface::class,
            FileRepository::class
        );
        $this->app->bind(
            ImageRepositoryInterface::class,
            ImageRepository::class
        );
        $this->app->bind(
            PanelRepositoryInterface::class,
            PanelRepository::class
        );
        $this->app->bind(
            GroupRepositoryInterface::class,
            GroupRepository::class
        );
    }
}