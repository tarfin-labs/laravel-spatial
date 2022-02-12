<?php

declare(strict_types=1);

namespace TarfinLabs\LaravelSpatial\Tests;

use Illuminate\Support\Facades\Config;
use TarfinLabs\LaravelSpatial\Collections\PointCollection;
use TarfinLabs\LaravelSpatial\Types\LineString;
use TarfinLabs\LaravelSpatial\Types\Point;

class LineStringTest extends TestCase
{
    /** @test */
    public function it_sets_points_and_srid_in_constructor(): void
    {
        // 1. Arrange
        $points = [
            new Point(25.1515, 36.1212, 4326),
            new Point(37.2121, 46.6565, 4326),
        ];

        // 2. Act
        $lineString1 = new LineString(new PointCollection($points), 4326);
        $lineString2 = new LineString($points, 4326);

        // 3. Assert
        $this->assertInstanceOf(PointCollection::class, $lineString1->getPoints());
        $this->assertInstanceOf(PointCollection::class, $lineString2->getPoints());
        $this->assertSame(4326, $lineString1->getSrid());
        $this->assertSame(4326, $lineString2->getSrid());
        $this->assertEquals($lineString1, $lineString2);
    }

    /** @test */
    public function it_returns_default_srid_if_it_is_not_given_in_the_constructor(): void
    {
        // 1. Arrange
        $points = [
            new Point(25.1515, 36.1212, 4326),
            new Point(37.2121, 46.6565, 4326),
        ];

        // 2. Act
        $lineString = new LineString(new PointCollection($points));

        // 3. Assert
        $this->assertSame(0, $lineString->getSrid());
    }

    /** @test */
    public function it_returns_default_srid_in_config_if_it_is_not_null(): void
    {
        // 1. Arrange
        Config::set('laravel-spatial.default_srid', 4326);

        $points = [
            new Point(25.1515, 36.1212, 4326),
            new Point(37.2121, 46.6565, 4326),
        ];

        // 2. Act
        $lineString = new LineString(new PointCollection($points));

        // 3. Assert
        $this->assertSame(expected: 4326, actual: $lineString->getSrid());
    }

    /** @test */
    public function it_returns_linestring_as_wkt(): void
    {
        // 1. Arrange
        $points = [
            new Point(25.1515, 36.1212, 4326),
            new Point(37.2121, 46.6565, 4326),
        ];

        $lineString = new LineString($points);

        // 2. Act
        $wkt = $lineString->toWkt();

        // 3. Assert
        $this->assertSame("LINESTRING({$points[0]->toPair()},{$points[1]->toPair()})", $wkt);
    }

    /** @test */
    public function it_returns_linestring_as_array(): void
    {
        // 1. Arrange
        $points = [
            new Point(25.1515, 36.1212, 4326),
            new Point(37.2121, 46.6565, 4326),
        ];

        $lineString = new LineString($points, 4326);

        // 2. Act
        $array = $lineString->toArray();

        $expected = [
            'points' => [
                [
                    'lat'   => 25.1515,
                    'lng'   => 36.1212,
                    'srid'  => 4326,
                ],
                [
                    'lat'   => 37.2121,
                    'lng'   => 46.6565,
                    'srid'  => 4326,
                ],
            ],
            'srid' => 4326,
        ];

        // 3. Assert
        $this->assertSame($expected, $array);
    }
}