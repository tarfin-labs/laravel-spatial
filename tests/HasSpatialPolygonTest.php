<?php

namespace TarfinLabs\LaravelSpatial\Tests;

use PHPUnit\Framework\Attributes\Test;
use TarfinLabs\LaravelSpatial\Tests\TestModels\Place;

class HasSpatialPolygonTest extends TestCase
{
    #[Test]
    public function it_generates_sql_query_for_area_casted_attributes(): void
    {
        // 1. Arrange
        $place = new Place();
        $castedAttr = $place->getRegionCastedAttributes()->first();

        // 2. Act & Assert
        $this->assertEquals(
            expected: "select `places`.*, CONCAT(ST_AsText(places.$castedAttr, 'axis-order=long-lat'), ',', ST_SRID(places.$castedAttr)) as $castedAttr from `places`",
            actual: $place->query()->toSql()
        );
    }

    #[Test]
    public function it_returns_area_casted_attributes(): void
    {
        // 1. Arrange
        $place = new Place();

        // 2. Act
        $areaCastedAttributes = $place->getRegionCastedAttributes();

        // 3. Assert
        $this->assertEquals(collect(['area']), $areaCastedAttributes);
    }
}
