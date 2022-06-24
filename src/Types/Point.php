<?php

declare(strict_types=1);

namespace TarfinLabs\LaravelSpatial\Types;

class Point
{
    protected float $lat;

    protected float $lng;

    protected int $srid;

    public function __construct(float $lat = 0, float $lng = 0, ?int $srid = null)
    {
        $this->lat = $lat;
        $this->lng = $lng;

        $this->srid = is_null($srid)
            ? config('laravel-spatial.default_srid') ?? 0
            : $srid;
    }

    public function getLat(): float
    {
        return $this->lat;
    }

    public function getLng(): float
    {
        return $this->lng;
    }

    public function getSrid(): int
    {
        return $this->srid;
    }

    public function toWkt(): string
    {
        return sprintf('POINT(%s)', $this->toPair());
    }

    public function toPair(): string
    {
        return "{$this->getLng()} {$this->getLat()}";
    }

    public function toArray(): array
    {
        return [
            'lat'  => $this->lat,
            'lng'  => $this->lng,
            'srid' => $this->srid,
        ];
    }
}
