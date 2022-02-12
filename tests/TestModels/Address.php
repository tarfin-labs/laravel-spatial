<?php

declare(strict_types=1);

namespace TarfinLabs\LaravelSpatial\Tests\TestModels;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use TarfinLabs\LaravelSpatial\Casts\LineStringCast;
use TarfinLabs\LaravelSpatial\Casts\LocationCast;
use TarfinLabs\LaravelSpatial\Traits\HasSpatial;
use TarfinLabs\LaravelSpatial\Types\LineString;
use TarfinLabs\LaravelSpatial\Types\Point;

/**
 * Class Address
 *
 * @method void selectDistanceTo(Builder $query, string $column, Point $point)
 * @method void orderByDistanceTo(Builder $query, string $column, Point $point, string $direction = 'asc')
 * @method void withinDistanceTo(Builder $query, string $column, Point $point, int $distance)
 *
 * @property Point location
 * @property LineString line_string
 */
class Address extends Model
{
    use HasSpatial;

    protected $fillable = [
        'location',
        'line_string',
    ];

    public $casts = [
        'location'      => LocationCast::class,
        'line_string'   => LineStringCast::class,
    ];
}
