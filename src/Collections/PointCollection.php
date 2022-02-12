<?php

declare(strict_types=1);

namespace TarfinLabs\LaravelSpatial\Collections;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use TarfinLabs\LaravelSpatial\Types\Point;

class PointCollection extends Collection
{
    public function __construct(array $points = [])
    {
        $this->validatePoints($points);

        parent::__construct($points);
    }

    public function addPoint(Point $point): void
    {
        $this->items[] = $point;
    }

    private function validatePoints(array $points): void
    {
        foreach ($points as $point) {
            if (!$point instanceof Point) {
                throw new InvalidArgumentException(message: sprintf(
                    '%s must be a collection of %s',
                    get_class($this),
                    Point::class
                ));
            }
        }
    }
}