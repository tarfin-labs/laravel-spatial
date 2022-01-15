<?php

declare(strict_types=1);

namespace TarfinLabs\LaravelSpatial\Tests;

use PHPUnit\Framework\TestCase;
use TarfinLabs\LaravelSpatial\Types\Point;

class PointTest extends TestCase
{
    /** @test */
    public function it_returns_latitude_and_longitude(): void
    {
        // Arrange
        $lat = 25.1515;
        $lng = 36.1212;

        // Act
        $point = new Point(lat: $lat, lng: $lng);

        // Assert
        $this->assertSame(expected: 25.1515, actual: $point->getLat());
        $this->assertSame(expected: 36.1212, actual: $point->getLng());
    }
}