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

        if (class_exists(Type::class)) {
            // Prevent geometry type fields from throwing a 'type not found' error when changing them
            $geometries = [
                'point' => Point::class,
            ];
            $typeNames = array_keys(Type::getTypesMap());
            foreach ($geometries as $type => $class) {
                if (!in_array($type, $typeNames)) {
                    Type::addType($type, $class);
                    DB::connection()->registerDoctrineType($class, $type, $type);
                }
            }
        }
    }
}
