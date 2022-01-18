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
        // Arrange
        $lat = 25.1515;
        $lng = 36.1212;
        $srid = 4326;

        // Act
        $point = new Point(lat: $lat, lng: $lng, srid: $srid);

        // Assert
        $this->assertSame(expected: $lat, actual: $point->getLat());
        $this->assertSame(expected: $lng, actual: $point->getLng());
        $this->assertSame(expected: $srid, actual: $point->getSrid());
    }

    /** @test */
    public function it_returns_default_lat_lng_and_srid_if_they_are_not_given_to_constructor(): void
    {
        // Act
        $point = new Point();

        // Assert
        $this->assertSame(expected: 0.0, actual: $point->getLat());
        $this->assertSame(expected: 0.0, actual: $point->getLng());
        $this->assertSame(expected: 0, actual: $point->getSrid());
    }

    /** @test */
    public function it_returns_default_srid_in_config_if_it_is_not_null(): void
    {
        // Arrange
        Config::set('laravel-spatial.default_srid', 4326);

        // Act
        $point = new Point();

        // Assert
        $this->assertSame(expected: 0.0, actual: $point->getLat());
        $this->assertSame(expected: 0.0, actual: $point->getLng());
        $this->assertSame(expected: 4326, actual: $point->getSrid());
    }
}