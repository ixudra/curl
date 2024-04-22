# Changelog

All Notable changes to `ixudra/curl` will be documented in this file

## 6.22.2 - 2024-04-22
### Fixed
- Use correct default values for ssl_verifypeer

## 6.22.1 - 2022-07-31
### Fixed
- Use correct default values for http_build_query

## 6.22.0 - 2021-11-14
### Added
- Added HEAD request support

## 6.21.0 - 2020-09-22
### Added
- withAuthorization utility method
- withBearer utility method

## 6.20.0 - 2020-08-07
### Fixed
- Data is now passed as POST parameters instead of GET for `DELETE` REQUESTS

### Added
- Additional docblocks
- GET parameters are added to the URL on file download

## 6.19.0 - 2020-03-03
### Added
- Added `withConnectTimeout()` method

## 6.17.0 - 2019-09-13
### Added
- Added support for multiple response headers with the same name

## 6.16.0 - 2017-12-07
### Added
- Added method to return response headers in the response objects

## 6.15.1 - 2017-11-07
### Added
- Bugfix: wrong variable used

## 6.15.0 - 2017-11-06
### Added
- Added withProxy method

## 6.14.0 - 2017-10-30
### Added
- Laravel auto-discovery

## 6.13.0 - 2017-10-10
### Added
- Added content type to responseObject

## 6.12.2 - 2017-08-23
### Fixed
- Reverted type hinting in `withData()` method

## 6.12.1 - 2017-08-07
### Fixed
- Removed debug code
- Fixed undefined index bug

## 6.12.0 - 2017-08-06
### Added
- File uploads by path
- `returnResponseArray()` method
- `enableXDebug()` method for easy xDebug integration
- CHANGELOG file

### Updated
- README


