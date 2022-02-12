<?php

namespace TarfinLabs\LaravelSpatial\Tests;

use TarfinLabs\LaravelSpatial\Casts\LineStringCast;
use TarfinLabs\LaravelSpatial\Casts\LocationCast;
use TarfinLabs\LaravelSpatial\Tests\TestModels\Address;
use TarfinLabs\LaravelSpatial\Types\Point;

class HasSpatialTest extends TestCase
{
    /**
     * @test
     * @see
     */
    public function it_generates_sql_query_for_selectDistanceTo_scope(): void
    {
        // Arrange
        $address = new Address();
        $address->casts = ['location' => LocationCast::class];

        $castedAttr = $address->getGeometryCastedAttributes()->first();

        // Act
        $query = $address->selectDistanceTo($castedAttr, new Point());

        // Assert
        $this->assertEquals(
            expected: "select *, CONCAT(ST_AsText(addresses.$castedAttr, 'axis-order=long-lat'), ',', ST_SRID(addresses.$castedAttr)) as $castedAttr, ST_Distance(
            ST_SRID($castedAttr, ?),
            ST_SRID(Point(?, ?), ?)
        ) as distance from `addresses`",
            actual: $query->toSql()
        );
    }

    /**
     * @test
     * @see
     */
    public function it_generates_sql_query_for_withinDistanceTo_scope(): void
    {
        // 1. Arrange
        $address = new Address();
        $address->casts = ['location' => LocationCast::class];
        $castedAttr = $address->getGeometryCastedAttributes()->first();

        // 2. Act
        $query = $address->withinDistanceTo($castedAttr, new Point(), 10000);

        // 3. Assert
        $this->assertEquals(
            expected: "select *, CONCAT(ST_AsText(addresses.$castedAttr, 'axis-order=long-lat'), ',', ST_SRID(addresses.$castedAttr)) as $castedAttr from `addresses` where ST_Distance(
            ST_SRID($castedAttr, ?),
            ST_SRID(Point(?, ?), ?)
        ) <= ?",
            actual: $query->toSql()
        );
    }

    /**
     * @test
     * @see
     */
    public function it_generates_sql_query_for_orderByDistanceTo_scope(): void
    {
        // 1. Arrange
        $address = new Address();
        $address->casts = ['location' => LocationCast::class];
        $castedAttr = $address->getGeometryCastedAttributes()->first();

        // 2. Act
        $queryForAsc = $address->orderByDistanceTo($castedAttr, new Point());
        $queryForDesc = $address->orderByDistanceTo($castedAttr, new Point(), 'desc');

        // 3. Assert
        $this->assertEquals(
            expected: "select *, CONCAT(ST_AsText(addresses.$castedAttr, 'axis-order=long-lat'), ',', ST_SRID(addresses.$castedAttr)) as $castedAttr from `addresses` order by ST_Distance(
            ST_SRID($castedAttr, ?),
            ST_SRID(Point(?, ?), ?)
        ) asc",
            actual: $queryForAsc->toSql()
        );

        $this->assertEquals(
            expected: "select *, CONCAT(ST_AsText(addresses.$castedAttr, 'axis-order=long-lat'), ',', ST_SRID(addresses.$castedAttr)) as $castedAttr from `addresses` order by ST_Distance(
            ST_SRID($castedAttr, ?),
            ST_SRID(Point(?, ?), ?)
        ) desc",
            actual: $queryForDesc->toSql()
        );
    }

    /**
     * @test
     * @see
     */
    public function it_generates_sql_query_for_location_casted_attributes(): void
    {
        // 1. Arrange
        $address = new Address();
        $address->casts = ['location' => LocationCast::class];
        $castedAttr = $address->getGeometryCastedAttributes()->first();

        // 2. Act & Assert
        $this->assertEquals(
            expected: "select *, CONCAT(ST_AsText(addresses.$castedAttr, 'axis-order=long-lat'), ',', ST_SRID(addresses.$castedAttr)) as $castedAttr from `addresses`",
            actual: $address->newQuery()->toSql()
        );
    }

    /**
     * @test
     * @see
     */
    public function it_generates_sql_query_for_linestring_casted_attributes(): void
    {
        // 1. Arrange
        $address = new Address();
        $address->casts = ['line_string' => LineStringCast::class];
        $castedAttr = $address->getGeometryCastedAttributes()->first();

        // 2. Act & Assert
        $this->assertEquals(
            expected: "select *, CONCAT(ST_AsText(addresses.$castedAttr, 'axis-order=long-lat'), ',', ST_SRID(addresses.$castedAttr)) as $castedAttr from `addresses`",
            actual: $address->newQuery()->toSql()
        );
    }

    /**
     * @test
     * @see
     */
    public function it_returns_location_casted_attributes(): void
    {
        // 1. Arrange
        $address = new Address();

        // 2. Act
        $locationCastedAttributres = $address->getGeometryCastedAttributes();

        // 3. Assert
        $this->assertEquals(collect(['location', 'line_string']), $locationCastedAttributres);
    }
}
