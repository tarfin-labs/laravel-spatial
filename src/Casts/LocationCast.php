<?php

namespace TarfinLabs\LaravelSpatial\Casts;

use Exception;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\SerializesCastableAttributes;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use TarfinLabs\LaravelSpatial\Types\Point;

class LocationCast implements CastsAttributes, SerializesCastableAttributes
{
    public function get($model, string $key, $value, array $attributes): Point
    {
        $location = explode(',', str_replace(['POINT(', ')', ' '], ['', '', ','], $value));

        return new Point(lat: $location[1], lng: $location[0]);
    }

    public function set($model, string $key, $value, array $attributes): Expression
    {
        if (!$value instanceof Point) {
            throw new Exception(message: 'The location field must be instance of App\Services\Point.');
        }

        return DB::raw(value: "ST_GeomFromText('POINT({$value->getLng()} {$value->getLat()})')");
    }

    public function serialize($model, string $key, $value, array $attributes): array
    {
        return [
            'lat' => $value->getLat(),
            'lng' => $value->getLng(),
        ];
    }
}
