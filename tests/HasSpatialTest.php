<?php

namespace TarfinLabs\LaravelSpatial\Tests;

use Illuminate\Support\Collection;
use TarfinLabs\LaravelSpatial\Tests\Stubs\Models\Address;
use TarfinLabs\LaravelSpatial\Types\Point;

class HasSpatialTest extends TestCase
{
    public function test_scopeSelectDistanceTo(): void
    {
        // Arrange
        $address = new Address();
        $castedAttr = $address->getLocationCastedAttributes()->first();

        // Act
        $query = $address->selectDistanceTo($castedAttr, new Point());

        // Assert
        $this->assertEquals("select *, CONCAT(ST_AsText(addresses.{$castedAttr}), ',', ST_SRID(addresses.{$castedAttr})) as {$castedAttr}, ST_Distance(
            ST_SRID({$castedAttr}, ?),
            ST_SRID(Point(?, ?), ?)
        ) as distance from `addresses`", $query->toSql());
    }

    public function test_scopeWithinDistanceTo(): void
    {
        // Arrange
        $address = new Address();
        $castedAttr = $address->getLocationCastedAttributes()->first();

        // Act
        $query = $address->withinDistanceTo($castedAttr, new Point(), 10000);

        // Assert
        $this->assertEquals("select *, CONCAT(ST_AsText(addresses.{$castedAttr}), ',', ST_SRID(addresses.{$castedAttr})) as {$castedAttr} from `addresses` where ST_Distance(
            ST_SRID({$castedAttr}, ?),
            ST_SRID(Point(?, ?), ?)
        ) <= ?", $query->toSql());
    }

    public function test_scopeOrderByDistanceTo(): void
    {
        // Arrange
        $address = new Address();
        $castedAttr = $address->getLocationCastedAttributes()->first();

        // Act
        $queryForAsc = $address->orderByDistanceTo($castedAttr, new Point());
        $queryForDesc = $address->orderByDistanceTo($castedAttr, new Point(), 'desc');

        // Assert
        $this->assertEquals("select *, CONCAT(ST_AsText(addresses.{$castedAttr}), ',', ST_SRID(addresses.{$castedAttr})) as {$castedAttr} from `addresses` order by ST_Distance(
            ST_SRID({$castedAttr}, ?),
            ST_SRID(Point(?, ?), ?)
        ) asc", $queryForAsc->toSql());

        $this->assertEquals("select *, CONCAT(ST_AsText(addresses.{$castedAttr}), ',', ST_SRID(addresses.{$castedAttr})) as {$castedAttr} from `addresses` order by ST_Distance(
            ST_SRID({$castedAttr}, ?),
            ST_SRID(Point(?, ?), ?)
        ) desc", $queryForDesc->toSql());
    }

    public function test_newQuery(): void
    {
        // Arrange
        $address = new Address();
        $castedAttr = $address->getLocationCastedAttributes()->first();

        // Assert
        $this->assertEquals("select *, CONCAT(ST_AsText(addresses.{$castedAttr}), ',', ST_SRID(addresses.{$castedAttr})) as {$castedAttr} from `addresses`", $address->query()->toSql());
    }

    /**
     * @test
     * @see
     */
    public function it_returns_location_casted_attributes(): void
    {
        // Arrange
        $address = new Address();

        // Act
        $locationCastedAttributres = $address->getLocationCastedAttributes();

        // Assert
        $this->assertInstanceOf(Collection::class, $locationCastedAttributres);
        $this->assertEquals(collect(['location']), $locationCastedAttributres);
    }
}
