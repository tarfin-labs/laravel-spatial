<?php

declare(strict_types=1);

namespace TarfinLabs\LaravelSpatial\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use TarfinLabs\LaravelSpatial\Types\Point;
use TarfinLabs\LaravelSpatial\Types\Polygon;

class PolygonTest extends TestCase
{
    private function makeTrianglePoints(): array
    {
        return [
            new Point(lat: 0, lng: 0, srid: null),
            new Point(lat: 0, lng: 1, srid: null),
            new Point(lat: 1, lng: 1, srid: null),
        ];
    }

    #[Test]
    public function it_creates_a_polygon_with_points(): void
    {
        // 1. Arrange & Act
        $polygon = new Polygon($this->makeTrianglePoints(), 4326);

        // 2. Assert
        $this->assertCount(4, $polygon->getPoints());
        $this->assertSame(4326, $polygon->getSrid());
    }

    #[Test]
    public function it_auto_closes_the_polygon(): void
    {
        // 1. Arrange
        $points = $this->makeTrianglePoints();

        // 2. Act
        $polygon = new Polygon($points);

        // 3. Assert
        $allPoints = $polygon->getPoints();
        $first = $allPoints[0];
        $last = end($allPoints);
        $this->assertSame($first->getLat(), $last->getLat());
        $this->assertSame($first->getLng(), $last->getLng());
    }

    #[Test]
    public function it_does_not_double_close_an_already_closed_polygon(): void
    {
        // 1. Arrange
        $points = $this->makeTrianglePoints();
        $points[] = clone $points[0];

        // 2. Act
        $polygon = new Polygon($points);

        // 3. Assert
        $this->assertCount(4, $polygon->getPoints());
    }

    #[Test]
    public function it_throws_an_exception_with_fewer_than_3_points(): void
    {
        // 1. Expect
        $this->expectException(InvalidArgumentException::class);

        // 2. Act
        new Polygon([
            new Point(lat: 0, lng: 0, srid: null),
            new Point(lat: 1, lng: 1, srid: null),
        ]);
    }

    #[Test]
    public function it_throws_an_exception_with_non_point_values(): void
    {
        // 1. Expect
        $this->expectException(InvalidArgumentException::class);

        // 2. Act
        new Polygon(['not', 'points', 'here']);
    }

    #[Test]
    public function it_returns_polygon_as_wkt(): void
    {
        // 1. Arrange
        $polygon = new Polygon($this->makeTrianglePoints());

        // 2. Act
        $wkt = $polygon->toWkt();

        // 3. Assert
        $this->assertSame('POLYGON((0 0,1 0,1 1,0 0))', $wkt);
    }

    #[Test]
    public function it_returns_polygon_as_pairs(): void
    {
        // 1. Arrange
        $polygon = new Polygon($this->makeTrianglePoints());

        // 2. Act
        $pairs = $polygon->toPairs();

        // 3. Assert
        $this->assertSame('0 0,1 0,1 1,0 0', $pairs);
    }

    #[Test]
    public function it_returns_polygon_as_geometry(): void
    {
        // 1. Arrange
        $polygon = new Polygon($this->makeTrianglePoints(), 4326);

        // 2. Act
        $geometry = $polygon->toGeomFromText();

        // 3. Assert
        $this->assertSame("ST_GeomFromText('{$polygon->toWkt()}', 4326, 'axis-order=long-lat')", $geometry);
    }

    #[Test]
    public function it_returns_polygon_as_array(): void
    {
        // 1. Arrange
        $polygon = new Polygon($this->makeTrianglePoints(), 4326);

        // 2. Act
        $array = $polygon->toArray();

        // 3. Assert
        $this->assertArrayHasKey('points', $array);
        $this->assertArrayHasKey('srid', $array);
        $this->assertCount(4, $array['points']);
        $this->assertSame(4326, $array['srid']);
        $this->assertSame(0.0, $array['points'][0]['lat']);
        $this->assertSame(0.0, $array['points'][0]['lng']);
    }
}
