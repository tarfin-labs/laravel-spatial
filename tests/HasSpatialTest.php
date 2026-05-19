<?php

namespace TarfinLabs\LaravelSpatial\Tests;

use PHPUnit\Framework\Attributes\Test;
use TarfinLabs\LaravelSpatial\Tests\TestModels\Region;
use TarfinLabs\LaravelSpatial\Types\Point;

class HasSpatialTest extends TestCase
{
    #[Test]
    public function it_returns_both_location_and_area_casted_attributes(): void
    {
        // 1. Arrange
        $region = new Region();

        // 2. Act
        $locationAttributes = $region->getLocationCastedAttributes();
        $areaAttributes = $region->getRegionCastedAttributes();

        // 3. Assert
        $this->assertEquals(collect(['location']), $locationAttributes);
        $this->assertEquals(collect(['area']), $areaAttributes);
    }

    #[Test]
    public function it_generates_sql_query_with_both_spatial_columns_in_select(): void
    {
        // 1. Arrange
        $region = new Region();

        // 2. Act
        $sql = $region->query()->toSql();

        // 3. Assert
        $this->assertEquals(
            expected: "select `regions`.*, CONCAT(ST_AsText(regions.location, 'axis-order=long-lat'), ',', ST_SRID(regions.location)) as location, CONCAT(ST_AsText(regions.area, 'axis-order=long-lat'), ',', ST_SRID(regions.area)) as area from `regions`",
            actual: $sql
        );
    }

    #[Test]
    public function it_generates_sql_query_for_selectDistanceTo_with_both_spatial_columns(): void
    {
        // 1. Arrange
        $region = new Region();

        // 2. Act
        $query = $region->selectDistanceTo('location', new Point());

        // 3. Assert
        $this->assertEquals(
            expected: "select `regions`.*, CONCAT(ST_AsText(regions.location, 'axis-order=long-lat'), ',', ST_SRID(regions.location)) as location, CONCAT(ST_AsText(regions.area, 'axis-order=long-lat'), ',', ST_SRID(regions.area)) as area, ST_Distance(ST_SRID(location, ?), ST_SRID(Point(?, ?), ?)) as distance from `regions`",
            actual: $query->toSql()
        );
    }

    #[Test]
    public function it_generates_sql_query_for_withinDistanceTo_with_both_spatial_columns(): void
    {
        // 1. Arrange
        $region = new Region();

        // 2. Act
        $query = $region->withinDistanceTo('location', new Point(), 10000);

        // 3. Assert
        $this->assertEquals(
            expected: "select `regions`.*, CONCAT(ST_AsText(regions.location, 'axis-order=long-lat'), ',', ST_SRID(regions.location)) as location, CONCAT(ST_AsText(regions.area, 'axis-order=long-lat'), ',', ST_SRID(regions.area)) as area from `regions` where ST_AsText(location) != ? and ST_Distance(ST_SRID(location, ?), ST_SRID(Point(?, ?), ?)) <= ?",
            actual: $query->toSql()
        );
    }

    #[Test]
    public function it_generates_sql_query_for_orderByDistanceTo_with_both_spatial_columns(): void
    {
        // 1. Arrange
        $region = new Region();

        // 2. Act
        $queryForAsc = $region->orderByDistanceTo('location', new Point());
        $queryForDesc = $region->orderByDistanceTo('location', new Point(), 'desc');

        // 3. Assert
        $this->assertEquals(
            expected: "select `regions`.*, CONCAT(ST_AsText(regions.location, 'axis-order=long-lat'), ',', ST_SRID(regions.location)) as location, CONCAT(ST_AsText(regions.area, 'axis-order=long-lat'), ',', ST_SRID(regions.area)) as area from `regions` order by ST_Distance(ST_SRID(location, ?), ST_SRID(Point(?, ?), ?)) asc",
            actual: $queryForAsc->toSql()
        );

        $this->assertEquals(
            expected: "select `regions`.*, CONCAT(ST_AsText(regions.location, 'axis-order=long-lat'), ',', ST_SRID(regions.location)) as location, CONCAT(ST_AsText(regions.area, 'axis-order=long-lat'), ',', ST_SRID(regions.area)) as area from `regions` order by ST_Distance(ST_SRID(location, ?), ST_SRID(Point(?, ?), ?)) desc",
            actual: $queryForDesc->toSql()
        );
    }
}
