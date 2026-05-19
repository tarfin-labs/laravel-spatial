<?php

declare(strict_types=1);

namespace TarfinLabs\LaravelSpatial\Tests\TestModels;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use TarfinLabs\LaravelSpatial\Casts\RegionCast;
use TarfinLabs\LaravelSpatial\Casts\LocationCast;
use TarfinLabs\LaravelSpatial\Traits\HasSpatial;
use TarfinLabs\LaravelSpatial\Types\Point;
use TarfinLabs\LaravelSpatial\Types\Polygon;

/**
 * Class Region
 *
 * @method void selectDistanceTo(Builder $query, string $column, Point $point)
 * @method void orderByDistanceTo(Builder $query, string $column, Point $point, string $direction = 'asc')
 * @method void withinDistanceTo(Builder $query, string $column, Point $point, int $distance)
 *
 * @property Point location
 * @property Polygon area
 */
class Region extends Model
{
    use HasSpatial;

    protected $fillable = [
        'location',
        'area',
    ];

    protected $casts = [
        'location' => LocationCast::class,
        'area'     => RegionCast::class,
    ];
}
