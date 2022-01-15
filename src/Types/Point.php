<?php

declare(strict_types=1);

namespace TarfinLabs\LaravelSpatial\Types;

class Point
{
    protected float $lat;

    protected float $lng;

    public function __construct(float $lat = 0, float $lng = 0)
    {
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
}
