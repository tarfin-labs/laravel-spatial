<?php

declare(strict_types=1);

namespace TarfinLabs\LaravelSpatial\Tests;

use Illuminate\Support\Facades\Config;
use TarfinLabs\LaravelSpatial\Types\Point;

class PointTest extends TestCase
{
    /** @test */
    public function it_sets_lat_lng_and_srid_in_constructor(): void
    {
        // 1. Arrange
        $lat = 25.1515;
        $lng = 36.1212;
        $srid = 4326;

        // 2. Act
        $point = new Point(lat: $lat, lng: $lng, srid: $srid);

        // 3. Assert
        $this->assertSame(expected: $lat, actual: $point->getLat());
        $this->assertSame(expected: $lng, actual: $point->getLng());
        $this->assertSame(expected: $srid, actual: $point->getSrid());
    }

    /** @test */
    public function it_returns_default_lat_lng_and_srid_if_they_are_not_given_in_the_constructor(): void
    {
        // 1. Act
        $point = new Point();

        // 2. Assert
        $this->assertSame(expected: 0.0, actual: $point->getLat());
        $this->assertSame(expected: 0.0, actual: $point->getLng());
        $this->assertSame(expected: 0, actual: $point->getSrid());
    }

    /** @test */
    public function it_returns_default_srid_in_config_if_it_is_not_null(): void
    {
        // 1. Arrange
        Config::set('laravel-spatial.default_srid', 4326);

        // 2. Act
        $point = new Point();

        // 3. Assert
        $this->assertSame(expected: 0.0, actual: $point->getLat());
        $this->assertSame(expected: 0.0, actual: $point->getLng());
        $this->assertSame(expected: 4326, actual: $point->getSrid());
    }

    /** @test */
    public function it_returns_point_as_wkt(): void
    {
        // 1. Arrange
        $point = new Point(25.1515, 36.1212, 4326);

        // 2. Act
        $wkt = $point->toWkt();

        // 3. Assert
        $this->assertSame("POINT({$point->getLng()} {$point->getLat()})", $wkt);
    }

    /** @test */
    public function it_returns_point_as_pair(): void
    {
        // 1. Arrange
        $point = new Point(25.1515, 36.1212, 4326);

        // 2. Act
        $pair = $point->toPair();

        // 3. Assert
        $this->assertSame("{$point->getLng()} {$point->getLat()}", $pair);
    }

    /**
     * @test
     * @see
     */
    public function it_returns_points_as_geometry(): void
    {
        // 1. Arrange
        $point = new Point(25.1515, 36.1212, 4326);

        // 2. Act
        $geometry = $point->toGeomFromText();

        // 3. Assert
        $this->assertSame("ST_GeomFromText('{$point->toWkt()}', {$point->getSrid()}, 'axis-order=long-lat')", $geometry);
    }

    /** @test */
    public function it_returns_points_as_array(): void
    {
        // 1. Arrange
        $point = new Point(25.1515, 36.1212, 4326);

        // 2. Act
        $array = $point->toArray();

        $expected = [
            'lat'   => $point->getLat(),
            'lng'   => $point->getLng(),
            'srid'  => $point->getSrid(),
        ];

        // 3. Assert
        $this->assertSame($expected, $array);
    }
}
