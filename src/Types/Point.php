<?php

declare(strict_types=1);

namespace TarfinLabs\LaravelSpatial\Types;

class Point extends Geometry
{
    protected float $lat;

    protected float $lng;

    public function __construct(float $lat = 0, float $lng = 0, ?int $srid = null)
    {
        parent::__construct($srid);

        $this->lat = $lat;
        $this->lng = $lng;
    }

    public function getLat(): float
    {
        return $this->lat;
    }

    public function getLng(): float
    {
        return $this->lng;
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
            'lat'   => $this->lat,
            'lng'   => $this->lng,
            'srid'  => $this->srid,
        ];
    }
}
