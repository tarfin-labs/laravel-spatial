<?php

namespace TarfinLabs\LaravelSpatial\Tests;

use Exception;
use Illuminate\Support\Facades\DB;
use TarfinLabs\LaravelSpatial\Casts\LocationCast;
use TarfinLabs\LaravelSpatial\Tests\TestModels\Address;
use TarfinLabs\LaravelSpatial\Types\Point;

class LocationCastTest extends TestCase
{
    public function test_setting_location_to_a_non_point_value(): void
    {
        // Arrange
        $address = new Address();

        $this->expectException(Exception::class);

        // Act
        $address->location = 'dummy';
    }

    public function test_setting_location_to_a_point(): void
    {
        $address = new Address();
        $point = new Point(27.1234, 39.1234);

        $cast = new LocationCast();
        $response = $cast->set($address, 'location', $point, $address->getAttributes());

        // Assert
        $this->assertEquals(DB::raw("ST_GeomFromText('POINT({$point->getLng()} {$point->getLat()})')"), $response);
    }

    public function test_getting_location(): void
    {
        // Arrange
        $address = new Address();
        $point = new Point(27.1234, 39.1234);

        // Act
        $address->location = $point;
        $address->save();

        // Assert
        $this->assertInstanceOf(Point::class, $address->location);
        $this->assertEquals($point->getLat(), $address->location->getLat());
        $this->assertEquals($point->getLng(), $address->location->getLng());
        $this->assertEquals($point->getSrid(), $address->location->getSrid());
    }

    public function test_serialize_location(): void
    {
        // Arrange
        $address = new Address();
        $point = new Point(27.1234, 39.1234);

        // Act
        $address->location = $point;
        $address->save();

        // Assert
        $array = $address->toArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('location', $array);
        $this->assertArrayHasKey('lat', $array['location']);
        $this->assertArrayHasKey('lng', $array['location']);
        $this->assertArrayHasKey('srid', $array['location']);

        $this->assertJson($address->toJson());
    }
}
