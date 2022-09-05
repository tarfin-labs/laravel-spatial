<?php

namespace TarfinLabs\LaravelSpatial\Tests;

use TarfinLabs\LaravelSpatial\LaravelSpatialServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelSpatialServiceProvider::class,
        ];
    }
}
