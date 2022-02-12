<?php

namespace TarfinLabs\LaravelSpatial\Types;

abstract class Geometry
{
    protected int $srid;

    public function __construct(?int $srid)
    {
        $this->srid = is_null($srid)
            ? config('laravel-spatial.default_srid') ?? 0
            : $srid;
    }

    abstract public function toWkt(): string;

    public function getSrid(): int
    {
        return $this->srid;
    }
}