# Changelog

All notable changes to `laravel-spatial` will be documented in this file

## 1.3.0 - 2022-06-24
- `toWkt()` method added to `Point` class for getting the coordinates as well known text.
- `toPair()` method added to `Point` class for getting the coordinates as pair.
- `toArray()` method added to `Point` class for getting the coordinates as array.

## 1.2.0 - 2022-02-08
- Laravel 9 support added.

# 1.1.2 - 2022-03-08
- Bug fixed for casting location columns that is null.

# 1.1.1 - 2022-01-20
- `axis-order` added to setter of `LocationCast`.
- `axis-order` added to query in `HasSpatial` trait.

# 1.1.0 - 2022-01-20
- `Point` type added to Doctrine mapped types in the service provider.
- SRID support added to setter of `LocationCast`.

## 1.0.0 - 2022-01-18
- initial release
