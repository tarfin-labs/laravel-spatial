<?php

namespace TarfinLabs\LaravelSpatial\Tests;

use Exception;
use Illuminate\Support\Facades\DB;
use TarfinLabs\LaravelSpatial\Casts\LineStringCast;
use TarfinLabs\LaravelSpatial\Tests\TestModels\Address;
use TarfinLabs\LaravelSpatial\Types\LineString;
use TarfinLabs\LaravelSpatial\Types\Point;

class LineStringCastTest extends TestCase
{
    /** @test */
    public function it_throws_an_exception_if_casted_attribute_set_to_a_non_linestring_value(): void
    {
        // 1. Arrange
        $address = new Address();

        // 2. Expect
        $this->expectException(Exception::class);

        // 3. Act
        $address->line_string = 'dummy';
    }

    /** @test */
    public function it_can_set_the_casted_attribute_to_a_linestring(): void
    {
        // 1. Arrange
        $address = new Address();

        $points = [
            new Point(25.1515, 36.1212),
            new Point(37.2121, 46.6565),
        ];

        $lineString = new LineString($points);

        $cast = new LineStringCast();

        // 2. Act
        $response = $cast->set($address, 'line_string', $lineString, $address->getAttributes());

        // 3. Assert
        $this->assertEquals(DB::raw("ST_GeomFromText('{$lineString->toWkt()}')"), $response);
    }

    /** @test */
    public function it_can_set_the_casted_attribute_to_a_linestring_with_srid(): void
    {
        // 1. Arrange
        $address = new Address();

        $points = [
            new Point(25.1515, 36.1212),
            new Point(37.2121, 46.6565),
        ];

        $lineString = new LineString($points, 4326);

        $cast = new LineStringCast();

        // 2. Act
        $response = $cast->set($address, 'line_string', $lineString, $address->getAttributes());

        // 3. Assert
        $this->assertEquals(DB::raw("ST_GeomFromText('{$lineString->toWkt()}', {$lineString->getSrid()}, 'axis-order=long-lat')"), $response);
    }

    /** @test */
    public function it_can_get_a_casted_attribute(): void
    {
        // 1. Arrange
        $address = new Address();

        $points = [
            new Point(25.1515, 36.1212),
            new Point(37.2121, 46.6565),
        ];

        $lineString = new LineString($points, 4326);

        // 2. Act
        $address->location = new Point();
        $address->line_string = $lineString;
        $address->save();

        // 3. Assert
        $this->assertInstanceOf(LineString::class, $address->line_string);
        $this->assertEquals($lineString->getPoints(), $address->line_string->getPoints());
        $this->assertEquals($lineString->getSrid(), $address->line_string->getSrid());
    }

    /** @test */
    public function it_can_serialize_a_casted_attribute(): void
    {
        // 1. Arrange
        $address = new Address();

        $points = [
            new Point(25.1515, 36.1212),
            new Point(37.2121, 46.6565),
        ];

        $lineString = new LineString($points, 4326);

        // 2. Act
        $address->location = new Point();
        $address->line_string = $lineString;
        $address->save();

        // 3. Assert
        $array = $address->toArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('line_string', $array);
        $this->assertArrayHasKey('points', $array['line_string']);
        $this->assertArrayHasKey('srid', $array['line_string']);

        $this->assertJson($address->toJson());
    }
}