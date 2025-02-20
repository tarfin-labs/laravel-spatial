# Laravel Spatial

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tarfin-labs/laravel-spatial.svg?style=flat-square)](https://packagist.org/packages/tarfin-labs/laravel-spatial)
[![Total Downloads](https://img.shields.io/packagist/dt/tarfin-labs/laravel-spatial.svg?style=flat-square)](https://packagist.org/packages/tarfin-labs/laravel-spatial)
![GitHub Actions](https://github.com/tarfin-labs/laravel-spatial/actions/workflows/main.yml/badge.svg)

This is a Laravel package to work with geospatial data types and functions.

It supports only MySQL Spatial Data Types and Functions, other RDBMS is on the roadmap.

### Laravel Compatibility

| Version | Supported Laravel Versions |
|---------|----------------------------|
| `3.x`   | `^11.0`, `^12.0`           |
| `2.x`   | `^8.0, ^9.0, ^10.0`        |

**Supported data types:**
- `Point`

**Available Scopes:**
- `withinDistanceTo($column, $coordinates, $distance)`
- `selectDistanceTo($column, $coordinates)`
- `orderByDistanceTo($column, $coordinates, 'asc')`

***
## Installation

You can install the package via composer:

```bash
composer require tarfin-labs/laravel-spatial
```

## Usage
Generate a new model with a migration file:

```bash
php artisan make:model Address --migration
```

### 1- Migrations:

```
### For Laravel 11 and Above Versions

From Laravel 11 onwards, migrations are created as follows:

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->geography('location', subtype: 'point');
        })
    }

}
```
In Laravel 11, the methods **point**, **lineString**, **polygon**, **geometryCollection**, **multiPoint**, **multiLineString**, and **multiPolygon** have been removed. Therefore, we are updating to use the **geography** method instead. The `geography` method sets the default SRID value to 4326.

#### Issue with adding a new location column with index to an existing table:
When adding a new location column with an index in Laravel, it can be troublesome if you have existing data. One common mistake is trying to set a default value for the new column using `->default(new Point(0, 0, 4326))`. However, `POINT` columns cannot have a default value, which can cause issues when trying to add an index to the column, as indexed columns cannot be nullable.

To solve this problem, it is recommended to perform a two-step migration like following:

### For Laravel 11 and Above Versions

```php
public function up()
{
    // Add the new location column as nullable
    Schema::table('table', function (Blueprint $table) {
        $table->geography('location', 'point')->nullable();
    });

    // In the second go, set 0,0 values, make the column not null and finally add the spatial index
    Schema::table('table', function (Blueprint $table) {
        DB::statement("UPDATE `addresses` SET `location` = ST_GeomFromText('POINT(0 0)', 4326);");

        DB::statement("ALTER TABLE `table` CHANGE `location` `location` POINT NOT NULL;");

        $table->spatialIndex('location');
    });
}
```

***

### 2- Models:

Fill the `$fillable`, `$casts` arrays in the model:

```php
use Illuminate\Database\Eloquent\Model;
use TarfinLabs\LaravelSpatial\Casts\LocationCast;
use TarfinLabs\LaravelSpatial\Traits\HasSpatial;

class Address extends Model {

    use HasSpatial;

    protected $fillable = [
        'id',
        'name',
        'address',
        'location',
    ];
    
    protected array $casts = [
        'location' => LocationCast::class
    ];

}
```

### 3- Spatial Data Types:

#### ***Point:***
`Point` represents the coordinates of a location and contains `latitude`, `longitude`, and `srid` properties.

At this point, it is crucial to understand what SRID is. Each spatial instance has a spatial reference identifier (SRID). The SRID corresponds to a spatial reference system based on the specific ellipsoid used for either flat-earth mapping or round-earth mapping. A spatial column can contain objects with different SRIDs.

>For details about SRID you can follow the link:
https://en.wikipedia.org/wiki/Spatial_reference_system

- Default value of `latitude`, `longitude` parameters is `0.0`.
- Default value of `srid` parameter is `0`.

```php
use TarfinLabs\LaravelSpatial\Types\Point;

$location = new Point(lat: 28.123456, lng: 39.123456, srid: 4326);

$location->getLat(); // 28.123456
$location->getLng(); // 39.123456
$location->getSrid(); // 4326
```

You can override the default SRID via the `laravel-spatial` config file. To do that, you should publish the config file using `vendor:publish` artisan command:

```bash
php artisan vendor:publish --provider="TarfinLabs\LaravelSpatial\LaravelSpatialServiceProvider"
```

After that, you can change the value of `default_srid` in `config/laravel-spatial.php`

```php 
return [
    'default_srid' => 4326,
];
```
***
#### Configuring WKT options
By default, this package uses the `longitude latitude` order for the coordinate values in the WKT format used by spatial functions. This is necessary for some versions of MySQL, which will interpret coordinate pairs as lat-long unless the `axis-order` option is explicitly set to `long-lat`.

However, MariaDB reads WKT values as `long-lat` by default, and its spatial functions like `ST_GeomFromText` and `ST_DISTANCE` do not accept an `options` parameter like their MySQL counterparts. This means that using the package with MariaDB will result in a `Syntax error or access violation: 1582 Incorrect parameter count in the call to native function 'ST_GeomFromText'` exception.

To address this issue, we have added a `with_wkt_options` parameter to the config file that can be used to override the default options. This property can be set to `false` to remove the options parameter entirely, which fixes the errors when using MariaDB.

```php 
return [
    'with_wkt_options' => true,
];
```
***
#### Bulk Operations
In order to insert or update several rows with spatial data in one query using the `upsert()` method in Laravel, the package requires a workaround solution to avoid the error `Object of class TarfinLabs\LaravelSpatial\Types\Point could not be converted to string`. 

The solution is to use the `toGeomFromText()` method to convert the `Point` object to a WKT string, and then use `DB::raw()` to create a raw query string.

Here's an example of how to use this workaround in your code:

```php
use TarfinLabs\LaravelSpatial\Types\Point;

$points = [
    ['external_id' => 5, 'location' => DB::raw((new Point(lat: 40.73, lng: -73.93))->toGeomFromText())],
    ['external_id' => 7, 'location' => DB::raw((new Point(lat: -37.81, lng: 144.96))->toGeomFromText())],
];

Property::upsert($points, ['external_id'], ['location']);
```


***
### 4- Scopes:

#### ***withinDistanceTo()***

You can use the `withinDistanceTo()` scope to filter locations by given distance:

To filter addresses within the range of 10 km from the given coordinate:

```php
use TarfinLabs\LaravelSpatial\Types\Point;
use App\Models\Address;

Address::query()
       ->withinDistanceTo('location', new Point(lat: 25.45634, lng: 35.54331), 10000)
       ->get();
```

#### ***selectDistanceTo()***

You can get the distance between two points by using `selectDistanceTo()` scope. The distance will be in meters:

```php
use TarfinLabs\LaravelSpatial\Types\Point;
use App\Models\Address;

Address::query()
       ->selectDistanceTo('location', new Point(lat: 25.45634, lng: 35.54331))
       ->get();
```

#### ***orderByDistanceTo()***

You can order your models by distance to given coordinates:

```php
use TarfinLabs\LaravelSpatial\Types\Point;
use App\Models\Address;

// ASC
Address::query()
       ->orderByDistanceTo('location', new Point(lat: 25.45634, lng: 35.54331))
       ->get();

// DESC
Address::query()
       ->orderByDistanceTo('location', new Point(lat: 25.45634, lng: 35.54331), 'desc')
       ->get();
```

#### Get latitude and longitude of the location:

```php
use App\Models\Address;

$address = Address::find(1);
$address->location; // TarfinLabs\LaravelSpatial\Types\Point

$address->location->getLat();
$address->location->getLng();
```

#### Create a new address with location:

```php
use App\Models\Address;

Address::create([
    'name'      => 'Bag End',
    'address'   => '1 Bagshot Row, Hobbiton, Shire',
    'location'  => new Point(lat: 25.45634, lng: 35.54331),
]);
```

#### Usage in Resource:
To get an array representation of a location-casted field from a resource, you can return `parent::toArray($request)`.

If you need to return a custom array from a resource, you can use the `toArray()` method of the `Point` object.

```php
class LocationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'location' => $this->location->toArray(),
        ];
    }
}
```

Either way, you will get the following output for the location casted field:

```json
{
    "lat": 25.45634,
    "lng": 35.54331,
    "srid": 4326
}
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email development@tarfin.com instead of using the issue tracker.

## Credits

-   [Turan Karatug](https://github.com/tarfin-labs)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

