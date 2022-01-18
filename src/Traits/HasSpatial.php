<?php

namespace TarfinLabs\LaravelSpatial\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use TarfinLabs\LaravelSpatial\Casts\LocationCast;
use TarfinLabs\LaravelSpatial\Types\Point;

trait HasSpatial
{
    public function scopeSelectDistanceTo(Builder $query, string $column, Point $point): void
    {
        if (is_null($query->getQuery()->columns)) {
            $query->select('*');
        }

        $query->selectRaw("ST_Distance(
            ST_SRID({$column}, ?),
            ST_SRID(Point(?, ?), ?)
        ) as distance", [
            $point->getSrid(),
            $point->getLng(),
            $point->getLat(),
            $point->getSrid(),
        ]);
    }

    public function scopeWithinDistanceTo(Builder $query, string $column, Point $point, int $distance): void
    {
        $query->whereRaw("ST_Distance(
            ST_SRID({$column}, ?),
            ST_SRID(Point(?, ?), ?)
        ) <= ?", [...[
            $point->getSrid(),
            $point->getLng(),
            $point->getLat(),
            $point->getSrid(),
        ], $distance]);
    }

    public function scopeOrderByDistanceTo(Builder $query, string $column, Point $point, string $direction = 'asc'): void
    {
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $query->orderByRaw("ST_Distance(
            ST_SRID({$column}, ?),
            ST_SRID(Point(?, ?), ?)
        ) ".$direction, [
            $point->getSrid(),
            $point->getLng(),
            $point->getLat(),
            $point->getSrid(),
        ]);
    }

    public function newQuery(bool $excludeDeleted = true): Builder
    {
        $raw = '';

        foreach ($this->getLocationCastedProperties() as $column => $cast) {
            $raw .= "CONCAT(ST_AsText({$this->getTable()}.{$column}), ',', ST_SRID({$this->getTable()}.{$column})) as {$column}, ";
        }

        $raw = substr($raw, 0, -2);

        return parent::newQuery($excludeDeleted)->addSelect('*', DB::raw($raw));
    }

    public function getLocationCastedProperties(): Collection
    {
        return collect($this->getCasts())->filter(fn ($cast) => $cast === LocationCast::class);
    }
}
