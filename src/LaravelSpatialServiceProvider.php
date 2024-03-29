<?php

namespace TarfinLabs\LaravelSpatial;

use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use TarfinLabs\LaravelSpatial\Doctrine\Point;

class LaravelSpatialServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-spatial.php'),
            ], 'laravel-spatial');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-spatial');
    }
}
