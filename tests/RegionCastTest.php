<?php

namespace TarfinLabs\LaravelSpatial\Tests;

use Illuminate\Database\Query\Expression;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use TarfinLabs\LaravelSpatial\Casts\RegionCast;
use TarfinLabs\LaravelSpatial\Tests\TestModels\Place;
use TarfinLabs\LaravelSpatial\Types\Point;
use TarfinLabs\LaravelSpatial\Types\Polygon;

class RegionCastTest extends TestCase
{
    private function makeTrianglePolygon(): Polygon
    {
        return new Polygon([
            new Point(lat: 0, lng: 0, srid: null),
            new Point(lat: 0, lng: 1, srid: null),
            new Point(lat: 1, lng: 1, srid: null),
        ], 4326);
    }

    #[Test]
    public function it_throws_an_exception_if_casted_attribute_set_to_a_non_polygon_value(): void
    {
        // 1. Arrange
        $address = new Place();

        // 2. Expect
        $this->expectException(InvalidArgumentException::class);

        // 3. Act
        $address->area = 'dummy';
    }

    #[Test]
    public function it_can_set_the_casted_attribute_to_a_polygon(): void
    {
        // 1. Arrange
        $address = new Place();
        $polygon = $this->makeTrianglePolygon();

        $cast = new RegionCast();

        // 2. Act
        $response = $cast->set($address, 'area', $polygon, $address->getAttributes());

        // 3. Assert
        $this->assertEquals(DB::raw("ST_GeomFromText('{$polygon->toWkt()}', 4326, 'axis-order=long-lat')"), $response);
    }

    #[Test]
    public function it_can_set_the_casted_attribute_to_a_polygon_with_srid(): void
    {
        // 1. Arrange
        $address = new Place();
        $polygon = new Polygon([
            new Point(lat: 0, lng: 0, srid: null),
            new Point(lat: 0, lng: 1, srid: null),
            new Point(lat: 1, lng: 1, srid: null),
        ], 4326);

        $cast = new RegionCast();

        // 2. Act
        $response = $cast->set($address, 'area', $polygon, $address->getAttributes());

        // 3. Assert
        $this->assertEquals(DB::raw("ST_GeomFromText('{$polygon->toWkt()}', {$polygon->getSrid()}, 'axis-order=long-lat')"), $response);
    }

    #[Test]
    public function it_can_get_a_casted_attribute(): void
    {
        // 1. Arrange
        $address = new Place();
        $polygon = $this->makeTrianglePolygon();

        // 2. Act
        $address->area = $polygon;
        $address->save();

        // 3. Assert
        $this->assertInstanceOf(Polygon::class, $address->area);
        $this->assertCount(count($polygon->getPoints()), $address->area->getPoints());
        $this->assertEquals($polygon->getSrid(), $address->area->getSrid());
    }

    #[Test]
    public function it_can_get_a_casted_attribute_using_expression(): void
    {
        // 1. Arrange
        $address = new Place();
        $polygon = $this->makeTrianglePolygon();

        // 2. Act
        $cast   = new RegionCast();
        $result = $cast->get($address, 'area', new Expression($polygon->toGeomFromText()), $address->getAttributes());

        // 3. Assert
        $this->assertInstanceOf(Polygon::class, $result);
        $this->assertCount(count($polygon->getPoints()), $result->getPoints());
        $this->assertEquals($polygon->getSrid(), $result->getSrid());
    }

    #[Test]
    public function it_returns_null_if_the_value_of_the_casted_column_is_null(): void
    {
        // 1. Arrange
        $address = new Place();

        // 2. Act
        $address->save();

        // 3. Assert
        $this->assertNull($address->area);
    }

    #[Test]
    public function it_can_serialize_a_casted_attribute(): void
    {
        // 1. Arrange
        $address = new Place();
        $polygon = $this->makeTrianglePolygon();

        // 2. Act
        $address->area = $polygon;
        $address->save();

        // 3. Assert
        $array = $address->toArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('area', $array);
        $this->assertArrayHasKey('points', $array['area']);
        $this->assertArrayHasKey('srid', $array['area']);

        $this->assertJson($address->toJson());
    }
}
