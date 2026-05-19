<?php

declare(strict_types=1);

namespace TarfinLabs\LaravelSpatial\Casts;

use InvalidArgumentException;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\SerializesCastableAttributes;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use TarfinLabs\LaravelSpatial\Types\Point;
use TarfinLabs\LaravelSpatial\Types\Polygon;

class RegionCast implements CastsAttributes, SerializesCastableAttributes
{
    public function get($model, string $key, $value, array $attributes): ?Polygon
    {
        if (is_null($value)) {
            return null;
        }

        $coordinates = $this->getCoordinates($model, $value);

        if (count($coordinates) > 1) {
            return $this->parsePolygon($coordinates[0], (int) $coordinates[1]);
        }

        return $this->parsePolygon($value);
    }

    public function set($model, string $key, $value, array $attributes): Expression
    {
        if (!$value instanceof Polygon) {
            throw new InvalidArgumentException(
                sprintf('The %s field must be instance of %s', $key, Polygon::class)
            );
        }

        if ($value->getSrid() > 0) {
            return DB::raw($value->toGeomFromText());
        }

        return DB::raw(value: "ST_GeomFromText('{$value->toWkt()}')");
    }

    public function serialize($model, string $key, $value, array $attributes): array
    {
        return $value->toArray();
    }

    private function parsePolygon(string $wkt, int $srid = 0): Polygon
    {
        preg_match('/POLYGON\(\((.+)\)\)/', $wkt, $matches);

        $pairs = explode(',', $matches[1]);

        $points = array_map(function (string $pair) {
            $coords = explode(' ', trim($pair));

            return new Point(lat: (float) $coords[1], lng: (float) $coords[0], srid: null);
        }, $pairs);

        return new Polygon($points, $srid > 0 ? $srid : null);
    }

    private function getCoordinates($model, $value): array
    {
        if ($value instanceof Expression) {
            preg_match(
                pattern: "/ST_GeomFromText\(\s*'([^']+)'\s*(?:,\s*(\d+))?\s*(?:,\s*'([^']+)')?\s*\)/",
                subject: (string) $value->getValue($model->getConnection()->getQueryGrammar()),
                matches: $matches,
            );

            return [
                $matches[1],
                (int) ($matches[2] ?? 0),
            ];
        }

        $lastComma = strrpos($value, ',');

        return [
            substr($value, 0, $lastComma),
            (int) substr($value, $lastComma + 1),
        ];
    }
}
