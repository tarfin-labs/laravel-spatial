<?php

namespace TarfinLabs\LaravelSpatial\Tests\stubs;

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
