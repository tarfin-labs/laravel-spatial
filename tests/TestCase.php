<?php

namespace TarfinLabs\LaravelSpatial\Tests;

use TarfinLabs\LaravelSpatial\LaravelSpatialServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'port'      => env('DB_PORT', 3306),
            'database'  => 'laravel_spatial_test',
            'username'  => 'root',
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelSpatialServiceProvider::class,
        ];
    }
}
