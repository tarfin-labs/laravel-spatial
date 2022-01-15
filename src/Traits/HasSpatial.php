<?php

namespace TarfinLabs\LaravelSpatial\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use TarfinLabs\LaravelSpatial\Types\Point;

trait HasSpatial
{
    public function scopeSelectDistanceTo(Builder $query, string $column, Point $point): void
    {
        if (is_null($query->getQuery()->columns)) {
            $query->select('*');
        }

        $query->selectRaw("ST_Distance(
            ST_SRID({$column}, 4326),
            ST_SRID(Point(?, ?), 4326)
        ) as distance", [$point->getLng(), $point->getLat()]);
    }

    public function scopeWithinDistanceTo(Builder $query, string $column, Point $point, int $distance): void
    {
        $query->whereRaw("ST_Distance(
            ST_SRID({$column}, 4326),
            ST_SRID(Point(?, ?), 4326)
        ) <= ?", [...[$point->getLng(), $point->getLat()], $distance]);
    }

    public function scopeOrderByDistanceTo(Builder $query, Point $point, string $direction = 'asc'): void
    {
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $query->orderByRaw('ST_Distance(
            ST_SRID(location, 4326),
            ST_SRID(Point(?, ?), 4326)
        )'.$direction, [$point->getLng(), $point->getLat()]);
    }

    public function newQuery(bool $excludeDeleted = true): Builder
    {
        $raw = '';
        foreach ($this->geometry as $column) {
            $raw .= " ST_AsText({$this->getTable()}.{$column}) as {$column}, ";
        }
        $raw = substr($raw, 0, -2);

        return parent::newQuery($excludeDeleted)->addSelect('*', DB::raw($raw));
    }
}
