<?php

declare(strict_types=1);

namespace TarfinLabs\LaravelSpatial\Tests\TestModels;

use Illuminate\Database\Eloquent\Model;
use TarfinLabs\LaravelSpatial\Casts\RegionCast;
use TarfinLabs\LaravelSpatial\Traits\HasSpatial;
use TarfinLabs\LaravelSpatial\Types\Polygon;

/**
 * Class Place
 *
 * @property Polygon area
 */
class Place extends Model
{
    use HasSpatial;

    protected $fillable = [
        'area',
    ];

    protected $casts = [
        'area' => RegionCast::class,
    ];
}
