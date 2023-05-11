# Changelog

All notable changes to `laravel-spatial` will be documented in this file

## 1.7.0 - 2023-05-11
- PHP 8.2 support added.

## 1.6.1 - 2023-04-04
- Changed `toGeomFromTextString()` to `toGeomFromText()` in Readme.
- Fixed `with_wkt_options` configuration.

## 1.6.0 - 2023-03-30
- Added `MariaDB` support to spatial functions.
- Readme updated for adding a location column with default value to an existing table.
- `toGeomFromTextString()` method added to `Point` class for bulk insert/update operations.

## 1.4.2 - 2023-02-16
- Laravel 10 support added.

## 1.4.1 - 2022-09-05
- Doctrtine unknown type error fixed.

## 1.4.0 - 2022-07-24
- Locations that have zero points are excluded while getting the distance to a given location in `withingDistanceTo` scope.

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
