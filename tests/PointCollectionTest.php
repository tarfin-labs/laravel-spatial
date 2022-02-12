<?php

declare(strict_types=1);

namespace TarfinLabs\LaravelSpatial\Tests;

use InvalidArgumentException;
use TarfinLabs\LaravelSpatial\Collections\PointCollection;
use TarfinLabs\LaravelSpatial\Types\Point;

class PointCollectionTest extends TestCase
{
    /** @test */
    public function it_created_a_collection_of_points_in_constructor(): void
    {
        // 1. Arrange
        $points = [
            new Point(26.1212, 43.2121, 4326),
            new Point(36.5678, 41.7654, 4326),
        ];

        // 2. Act
        $collection = new PointCollection($points);

        // 3. Assert
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Point::class, $collection->first());
    }

    /** @test */
    public function it_throws_an_exception_if_any_of_items_not_a_point(): void
    {
        // 1. Arrange
        $points = [
            new Point(26.1212, 43.2121, 4326),
            'foo',
        ];

        // 2. Expect
        $this->expectException(InvalidArgumentException::class);

        // 3. Act
        new PointCollection($points);
    }

    /** @test */
    public function it_adds_a_point_item_to_the_collection(): void
    {
        // 1. Arrange
        $points = [
            new Point(26.1212, 43.2121, 4326),
            new Point(36.5678, 41.7654, 4326),
        ];

        $collection = new PointCollection($points);

        // 2. Act
        $collection->addPoint(new Point(15.4327, 34.2234, 4326));

        // 3. Assert
        $this->assertCount(3, $collection);
    }
}