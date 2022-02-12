<?php

declare(strict_types=1);

namespace TarfinLabs\LaravelSpatial\Types;

use TarfinLabs\LaravelSpatial\Collections\PointCollection;

class LineString extends Geometry
{
    protected PointCollection $points;

    public function __construct(PointCollection|array $points, ?int $srid = null)
    {
        parent::__construct($srid);

        $this->points = $points instanceof PointCollection
            ? $points
            : new PointCollection($points);
    }

    public function getPoints(): PointCollection
    {
        return $this->points;
    }

    public function toWkt(): string
    {
        return sprintf('LINESTRING(%s)', $this->pointString());
    }

    public function toArray(): array
    {
        return [
            'points' => array_map(function (Point $point) {
                return $point->toArray();
            }, $this->points->toArray()),
            'srid' => $this->srid,
        ];
    }

    private function pointString(): string
    {
        return implode(',', array_map(function (Point $point) {
            return $point->toPair();
        }, $this->points->toArray()));
    }
}