# Change Log
All notable changes to this project will be documented in this file.
Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [Unreleased][unreleased]

## [0.3.2] - 2016-08-17

### Added

 - A default value can be provided when trying to access slot values (#8)

### Changed

 - Responses now default to ending the session by default (#13)
 - Added `return $this` to several setter methods (#11)
 - Cleaned up code style

### Fixed

 - Fixed errors accessing slot values before the request was processed (#7)
 - Fixed card being overwritten in the response (#10)
 - Fixed exceptions being thrown when setting a valid card type (#15) 

### Security

## [0.3.1] - 2016-07-23

### Fixed

 - Fixed bug where SSML parameter was overwritten before use

## [0.3.0] - 2016-07-23

### Added

 - Added better support for SSML and playing audio files

### Fixed

 - Fixed typo in SSML template

## [0.2.9] - 2016-07-10

### Fixed

 - Set the router on new routes as required by 5.2 changes

### Removed

 - Removed aliases to solve issue with recursive attempts at resolving aliases

## [0.2.8] - 2016-07-08

### Added

 - Added support for SSML (#3)
 - Added `speechType` property to `AlexaResponse::say()` (#3)

### Changed

 - Bumped the `illuminate/routing` dependency to 5.2 (#1)
 
### Fixed

 - Fixed issue checking for `value` key in slots (#3)

## [0.2.7] - 2015-06-28

### Added

 - Added ability to automatically route responses to an Intent

## [0.2.6] - 2015-06-21

### Fixed

 - Fixed variable typo in `Speech::getText()`

## [0.2.5] - 2015-06-21

### Changed

 - Changed order of provider

## [0.2.4] - 2015-06-21

### Changed

 - 	Changed migration name to be less pretty and more worky

## [0.2.3] - 2015-06-20

### Changed

 - Made configuration available before other provider logic runs

## [0.2.2] - 2015-06-17

### Changed

 - Specified full path for publishing setting for migration
 - Improved the CSRF skip logic
 - Renamed `unsetCsrfMiddlware()` to `unsetCsrfMiddleware()`

## [0.2.1] - 2015-06-17

### Changed

 - Renamed `ALEXA_POSSIBLE_APP_IDS` environment variable to `ALEXA__APPLICATION_IDS`

## [0.2.0] - 2015-06-17

## [0.1.1] - 2015-05-04

## 0.1.0 - 2015-04-30

[unreleased]: https://github.com/develpr/alexa-app/compare/0.3.2...master
[0.3.2]: https://github.com/develpr/alexa-app/compare/0.3.1...0.3.2
[0.3.1]: https://github.com/develpr/alexa-app/compare/0.3.0...0.3.1
[0.3.0]: https://github.com/develpr/alexa-app/compare/0.2.9...0.3.0
[0.2.9]: https://github.com/develpr/alexa-app/compare/0.2.8...0.2.9
[0.2.8]: https://github.com/develpr/alexa-app/compare/0.1.7...0.2.8
[0.2.7]: https://github.com/develpr/alexa-app/compare/0.1.6...0.2.7
[0.2.6]: https://github.com/develpr/alexa-app/compare/0.1.5...0.2.6
[0.2.5]: https://github.com/develpr/alexa-app/compare/0.1.4...0.2.5
[0.2.4]: https://github.com/develpr/alexa-app/compare/0.1.3...0.2.4
[0.2.3]: https://github.com/develpr/alexa-app/compare/0.1.2...0.2.3
[0.2.2]: https://github.com/develpr/alexa-app/compare/0.1.1...0.2.2
[0.2.1]: https://github.com/develpr/alexa-app/compare/0.1.0...0.2.1
[0.2.0]: https://github.com/develpr/alexa-app/compare/0.1.1...0.2.0
[0.1.1]: https://github.com/develpr/alexa-app/compare/0.1.0...0.1.1
