<?php

namespace TarfinLabs\LaravelSpatial\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\SerializesCastableAttributes;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use TarfinLabs\LaravelSpatial\Collections\PointCollection;
use TarfinLabs\LaravelSpatial\Types\LineString;
use TarfinLabs\LaravelSpatial\Types\Point;

class LineStringCast implements CastsAttributes, SerializesCastableAttributes
{
    public function get($model, string $key, $value, array $attributes): LineString
    {
        $srid = explode('),', $value)[1];

        $points = array_filter(
            explode(',', str_replace(['LINESTRING(', ')'], ['', ''], $value)),
            function($item) {
                return Str::contains($item, ' ');
            }
        );

        $pointCollection = new PointCollection();

        foreach ($points as $point) {
            [$lng, $lat] = explode(' ', $point);
            $pointCollection->addPoint(new Point($lat, $lng, $srid));
        }

        return new LineString($pointCollection->toArray());
    }

    public function set($model, string $key, $value, array $attributes): Expression
    {
        if (!$value instanceof LineString) {
            throw new InvalidArgumentException(sprintf(
                'The %s field must be instance of %s',
                $key, LineString::class
            ));
        }

        $points = [];

        foreach ($value->getPoints() as $point) {
            $points[] = $point->toPair();
        }

        $linestring = sprintf('LINESTRING(%s)', implode(',', $points));

        if ($value->getSrid() > 0) {
            return DB::raw(
                value: "ST_GeomFromText('{$linestring}', {$value->getSrid()}, 'axis-order=long-lat')"
            );
        }

        return DB::raw(
            value: "ST_GeomFromText('{$linestring}')"
        );
    }

    public function serialize($model, string $key, $value, array $attributes): array
    {
        return $value->toArray();
    }
}