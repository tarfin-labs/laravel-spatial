<?php

namespace TarfinLabs\LaravelSpatial\Tests;

use TarfinLabs\LaravelSpatial\Tests\stubs\Address;
use TarfinLabs\LaravelSpatial\Types\Point;

class HasSpatialTest extends TestCase
{
    /** @test */
    public function test_scopeSelectDistanceTo(): void
    {
        // Arrange
        $address = new Address();

        // Act
        $query = $address->selectDistanceTo('location', new Point());

        // Assert
        $this->assertEquals("select *, CONCAT(ST_AsText(addresses.location), ',', ST_SRID(addresses.location)) as location, ST_Distance(
            ST_SRID(location, ?),
            ST_SRID(Point(?, ?), ?)
        ) as distance from `addresses`", $query->toSql());
    }

    /** @test */
    public function test_scopeWithinDistanceTo(): void
    {
        // Arrange
        $address = new Address();

        // Act
        $query = $address->withinDistanceTo('location', new Point(), 10000);

        // Assert
        $this->assertEquals("select *, CONCAT(ST_AsText(addresses.location), ',', ST_SRID(addresses.location)) as location from `addresses` where ST_Distance(
            ST_SRID(location, ?),
            ST_SRID(Point(?, ?), ?)
        ) <= ?", $query->toSql());
    }

    /** @test */
    public function test_scopeOrderByDistanceTo(): void
    {
        // Arrange
        $address = new Address();

        // Act
        $queryForAsc = $address->orderByDistanceTo('location', new Point());
        $queryForDesc = $address->orderByDistanceTo('location', new Point(), 'desc');

        // Assert
        $this->assertEquals("select *, CONCAT(ST_AsText(addresses.location), ',', ST_SRID(addresses.location)) as location from `addresses` order by ST_Distance(
            ST_SRID(location, ?),
            ST_SRID(Point(?, ?), ?)
        ) asc", $queryForAsc->toSql());

        $this->assertEquals("select *, CONCAT(ST_AsText(addresses.location), ',', ST_SRID(addresses.location)) as location from `addresses` order by ST_Distance(
            ST_SRID(location, ?),
            ST_SRID(Point(?, ?), ?)
        ) desc", $queryForDesc->toSql());
    }

    /** @test */
    public function test_newQuery(): void
    {
        // Arrange
        $address = new Address();

        // Assert
        $this->assertEquals("select *, CONCAT(ST_AsText(addresses.location), ',', ST_SRID(addresses.location)) as location from `addresses`", $address->query()->toSql());
    }
}
