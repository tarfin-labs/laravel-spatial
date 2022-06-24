<?php

namespace TarfinLabs\LaravelSpatial\Tests;

use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use TarfinLabs\LaravelSpatial\Casts\LocationCast;
use TarfinLabs\LaravelSpatial\Tests\TestModels\Address;
use TarfinLabs\LaravelSpatial\Types\Point;

class LocationCastTest extends TestCase
{
    /** @test */
    public function it_throws_an_exception_if_casted_attribute_set_to_a_non_point_value(): void
    {
        // 1. Arrange
        $address = new Address();

        // 2. Expect
        $this->expectException(InvalidArgumentException::class);

        // 3. Act
        $address->location = 'dummy';
    }

    /** @test */
    public function it_can_set_the_casted_attribute_to_a_point(): void
    {
        // 1. Arrange
        $address = new Address();
        $point = new Point(27.1234, 39.1234);

        $cast = new LocationCast();

        // 2. Act
        $response = $cast->set($address, 'location', $point, $address->getAttributes());

        // 3. Assert
        $this->assertEquals(DB::raw("ST_GeomFromText('{$point->toWkt()}')"), $response);
    }

    /** @test */
    public function it_can_set_the_casted_attribute_to_a_point_with_srid(): void
    {
        // 1. Arrange
        $address = new Address();
        $point = new Point(27.1234, 39.1234, 4326);

        $cast = new LocationCast();

        // 2. Act
        $response = $cast->set($address, 'location', $point, $address->getAttributes());

        // 3. Assert
        $this->assertEquals(DB::raw("ST_GeomFromText('{$point->toWkt()}', {$point->getSrid()}, 'axis-order=long-lat')"), $response);
    }

    /** @test */
    public function it_can_get_a_casted_attribute(): void
    {
        // 1. Arrange
        $address = new Address();
        $point = new Point(27.1234, 39.1234);

        // 2. Act
        $address->location = $point;
        $address->save();

        // 3. Assert
        $this->assertInstanceOf(Point::class, $address->location);
        $this->assertEquals($point->getLat(), $address->location->getLat());
        $this->assertEquals($point->getLng(), $address->location->getLng());
        $this->assertEquals($point->getSrid(), $address->location->getSrid());
    }

    /** @test */
    public function it_returns_null_if_the_value_of_the_casted_column_is_null(): void
    {
        // 1. Arrange
        $address = new Address();

        // 2. Act
        $address->save();

        // 3. Assert
        $this->assertNull($address->location);
    }

    /** @test */
    public function it_can_serialize_a_casted_attribute(): void
    {
        // 1. Arrange
        $address = new Address();
        $point = new Point(27.1234, 39.1234);

        // 2. Act
        $address->location = $point;
        $address->save();

        // 3. Assert
        $array = $address->toArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('location', $array);
        $this->assertArrayHasKey('lat', $array['location']);
        $this->assertArrayHasKey('lng', $array['location']);
        $this->assertArrayHasKey('srid', $array['location']);

        $this->assertJson($address->toJson());
    }
}
