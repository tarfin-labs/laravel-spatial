<?php

namespace TarfinLabs\LaravelSpatial\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

abstract class SpatialMigration extends Migration
{
    public function __construct()
    {
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('point', 'string');
    }
}