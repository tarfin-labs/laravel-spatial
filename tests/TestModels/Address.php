<?php

declare(strict_types=1);

namespace TarfinLabs\LaravelSpatial\Tests\TestModels;

use Illuminate\Database\Eloquent\Model;
use TarfinLabs\LaravelSpatial\Casts\LocationCast;
use TarfinLabs\LaravelSpatial\Traits\HasSpatial;

class Address extends Model
{
    use HasSpatial;

    protected $fillable = [
        'location',
    ];

    protected $casts = [
        'location' => LocationCast::class,
    ];
}
