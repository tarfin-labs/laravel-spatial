# Laravel Spatial

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tarfin-labs/laravel-spatial.svg?style=flat-square)](https://packagist.org/packages/tarfin-labs/laravel-spatial)
[![Total Downloads](https://img.shields.io/packagist/dt/tarfin-labs/laravel-spatial.svg?style=flat-square)](https://packagist.org/packages/tarfin-labs/laravel-spatial)
![GitHub Actions](https://github.com/tarfin-labs/laravel-spatial/actions/workflows/main.yml/badge.svg)

Laravel package to work with geospatial data types and functions.

## Installation

You can install the package via composer:

```bash
composer require tarfin-labs/laravel-spatial
```

## Usage

```php
use TarfinLabs\LaravelSpatial\Migrations\SpatialMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends SpatialMigration {
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->point('location');
            
            $table->spatialIndex(['location']);
        })
    }
}
```

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
    
    protected array $geometry = ['location'];
}
```

```php
use TarfinLabs\LaravelSpatial\Types\Point;
use App\Models\Address;

Address::query()
       ->withinDistanceTo('location', new Point(lat: 25.45634, lng: 35.54331), 10000)
       ->get();
```

```php
use TarfinLabs\LaravelSpatial\Types\Point;
use App\Models\Address;

Address::query()
       ->selectDistanceTo('location', new Point(lat: 25.45634, lng: 35.54331))
       ->get();
```

```php
use App\Models\Address;

$address = Address::find(1);
$address->location; // TarfinLabs\LaravelSpatial\Types\Point

$address->getLat();
$address->getLng();
```

```php
use App\Models\Address;

Address::create([
    'name'      => 'Home',
    'address'   => '1 Bagshot Row, Hobbiton, Shire',
    'location'  => new Point(lat: 25.45634, lng: 35.54331),
]);
```


### Testing

```bash
composer test
```

### Todo
- Proper documentation.
- Missing tests.

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

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
