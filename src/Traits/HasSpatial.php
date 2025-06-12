<?php

declare(strict_types=1);

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
            $query->select("{$this->getTable()}.*");
        }

        match (DB::connection()->getDriverName()) {
            'pgsql', 'mysql' => $this->selectDistanceToMysqlAndPostgres($query, $column, $point),
            'mariadb' => $this->selectDistanceToMariaDb($query, $column, $point),
            default => throw new \Exception('Unsupported database driver'),
        };
    }

    public function scopeWithinDistanceTo(Builder $query, string $column, Point $point, float $distance): void
    {
        match (DB::connection()->getDriverName()) {
            'pgsql', 'mysql' => $this->withinDistanceToMysqlAndPostgres($query, $column, $point, $distance),
            'mariadb' => $this->withinDistanceToMariaDb($query, $column, $point, $distance),
            default => throw new \Exception('Unsupported database driver'),
        };
    }

    public function scopeOrderByDistanceTo(Builder $query, string $column, Point $point, string $direction = 'asc'): void
    {
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        match (DB::connection()->getDriverName()) {
            'pgsql', 'mysql' => $this->orderByDistanceToMysqlAndPostgres($query, $column, $point, $direction),
            'mariadb' => $this->orderByDistanceToMariaDb($query, $column, $point, $direction),
            default => throw new \Exception('Unsupported database driver'),
        };
    }

    public function newQuery(): Builder
    {
        $raw = '';

        $wktOptions = config('laravel-spatial.with_wkt_options', true) === true
            ? ', \'axis-order=long-lat\''
            : '';

        foreach ($this->getLocationCastedAttributes() as $column) {
            $raw .= "CONCAT(ST_AsText({$this->getTable()}.{$column}$wktOptions), ',', ST_SRID({$this->getTable()}.{$column})) as {$column}, ";
        }

        $raw = substr($raw, 0, -2);

        return parent::newQuery()->addSelect("{$this->getTable()}.*", DB::raw($raw));
    }

    public function getLocationCastedAttributes(): Collection
    {
        return collect($this->getCasts())->filter(fn ($cast) => $cast === LocationCast::class)->keys();
    }

    private function selectDistanceToMysqlAndPostgres(Builder $query, string $column, Point $point): Builder
    {
        return $query->selectRaw(
            "ST_Distance(ST_SRID({$column}, ?), ST_SRID(Point(?, ?), ?)) as distance",
            [
                $point->getSrid(),
                $point->getLng(),
                $point->getLat(),
                $point->getSrid(),
            ]
        );
    }

    private function selectDistanceToMariaDb(Builder $query, string $column, Point $point): Builder
    {
        return $query->selectRaw(
            "ST_Distance(ST_SRID({$column}), ST_SRID(Point(?, ?))) as distance",
            [
                $point->getLng(),
                $point->getLat(),
            ]
        );
    }

    private function withinDistanceToMysqlAndPostgres(Builder $query, string $column, Point $point, float $distance): Builder
    {
        return $query
            ->whereRaw("ST_AsText({$column}) != ?", [
                'POINT(0 0)',
            ])
            ->whereRaw(
                "ST_Distance(ST_SRID({$column}, ?), ST_SRID(Point(?, ?), ?)) <= ?",
                [
                    ...[
                        $point->getSrid(),
                        $point->getLng(),
                        $point->getLat(),
                        $point->getSrid(),
                    ],
                    $distance,
                ]
            );
    }

    private function withinDistanceToMariaDb(Builder $query, string $column, Point $point, float $distance): Builder
    {
        return $query
            ->whereRaw("ST_AsText({$column}) != ?", [
                'POINT(0 0)',
            ])
            ->whereRaw(
                "ST_Distance(ST_SRID({$column}), ST_SRID(Point(?, ?))) <= ?",
                [
                    $point->getLng(),
                    $point->getLat(),
                    $distance,
                ]
            );
    }

    private function orderByDistanceToMysqlAndPostgres(Builder $query, string $column, Point $point, string $direction = 'asc'): Builder
    {
        return $query->orderByRaw(
            "ST_Distance(ST_SRID({$column}, ?), ST_SRID(Point(?, ?), ?)) " . $direction,
            [
                $point->getSrid(),
                $point->getLng(),
                $point->getLat(),
                $point->getSrid(),
            ]
        );
    }

    private function orderByDistanceToMariaDb(Builder $query, string $column, Point $point, string $direction = 'asc'): Builder
    {
        return $query->orderByRaw(
            "ST_Distance(ST_SRID({$column}), ST_SRID(Point(?, ?))) " . $direction,
            [
                $point->getLng(),
                $point->getLat(),
            ]
        );
    }
}
