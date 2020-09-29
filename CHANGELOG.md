# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [2.0.0]
### Fixed
- News link redirects are now working correctly on ILIAS 6
- Timeline links no longer result in a 404 error on ILIAS 6

### Deprecated
- PHP 5.x support
- ILIAS 5.3 support

### Removed
- ILIAS 5.2 support

## [1.1.5]
### Fixed
- Plugin max ILIAS version constraint

## [1.1.4]
### Added
- added token statistics (number of logins for past 30 90 180 days)
- configuration of REST client without requests to API

## [1.1.3]
### Added
- app theme icons

## [1.1.2]
### Fixed
- addition of table 'ui_uihk_pegasus_theme' 

## [1.1.1]
### Added
- external testing
### Changed
- modified feedback to user for failed tests (even if test fails, setup may still be ok)
### Fixed
- checking for presence of mysqli implementation
- installing and uninstalling the plugin

## [1.1.0]
### Added
- app theme coloring

## [1.0.2]
### Added
- testing for REST- and PegasusHelper-Plugins
- support for ILIAS 5.4

## [1.0.1]
### Fixed
- White page while opening ILIAS website with an auth token 

## [1.0.0]
### Added
- Changelog
- Download of documents with authentication token.
- Open personal news feed with authentication token.
### Changed
- Internal refactoring to speed up future releases.
### Fixed
- Fixed a bug which could lead to an access denied error with a valid authentication token.

## [0.0.11]
### Added
- Login
- Open links in ILIAS
- Open time line of a course in ILIAS


## [Unreleased]
### Added
### Changed
### Deprecated
### Removed
### Fixed
### Security
