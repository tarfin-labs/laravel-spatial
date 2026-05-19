<?php

declare(strict_types=1);

namespace TarfinLabs\LaravelSpatial\Types;

use InvalidArgumentException;

class Polygon
{
    /** @var Point[] */
    protected array $points;

    protected int $srid;

    protected string $wktOptions;

    /**
     * @param  Point[]  $points
     */
    public function __construct(array $points, ?int $srid = 4326)
    {
        foreach ($points as $point) {
            if (!$point instanceof Point) {
                throw new InvalidArgumentException('All points must be instances of ' . Point::class);
            }
        }

        if (count($points) < 3) {
            throw new InvalidArgumentException('A polygon must have at least 3 unique points.');
        }

        $first = $points[0];
        $last = end($points);

        if ($first->getLat() !== $last->getLat() || $first->getLng() !== $last->getLng()) {
            $points[] = clone $first;
        }

        $this->points = array_values($points);

        $this->srid = is_null($srid)
            ? config('laravel-spatial.default_srid') ?? 0
            : $srid;

        $this->wktOptions = config('laravel-spatial.with_wkt_options', true) === true
            ? ', \'axis-order=long-lat\''
            : '';
    }

    /**
     * @return Point[]
     */
    public function getPoints(): array
    {
        return $this->points;
    }

    public function getSrid(): int
    {
        return $this->srid;
    }

    public function toPairs(): string
    {
        return implode(',', array_map(fn (Point $point) => $point->toPair(), $this->points));
    }

    public function toWkt(): string
    {
        return sprintf('POLYGON((%s))', $this->toPairs());
    }

    public function toGeomFromText(): string
    {
        return "ST_GeomFromText('{$this->toWkt()}', {$this->getSrid()}{$this->wktOptions})";
    }

    public function toArray(): array
    {
        return [
            'points' => array_map(fn (Point $point) => [
                'lat' => $point->getLat(),
                'lng' => $point->getLng(),
            ], $this->points),
            'srid' => $this->srid,
        ];
    }
}
