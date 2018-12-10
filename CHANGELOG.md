# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
_

## [1.5.0] - 2018-12-10

### Added
- method: `OdooClient::model_execute_kw()`
- method: `Service/****Service::getRawResponse()`
- method: `OdooClient::getArrayType()`
- method: `OdooClient::getResponseEntry()`
- method: `OdooClient::setResponse()`
- method: `OdooClient::isResponseSuccess()`
- classes: `PermissionException.php`, `ResponseEntryException.php`
- constants: `CommonHandlerTrait::VERSION_ENTRY_****`
- this `CHANGELOG.md`

### Changed
- renamed method: `OdooClient::formatResponse()` become `checkResponse()`
- renamed method: `OdooClient::getCurrentRipcordClient()` become `getCurrentService()`
- method: `CommonHandlerTrait::version()` accepts an optional parameter to filter array output based on an entry
- constants: `OdooClient::ENDPOINT_****` now in `Service/****Service::ENPOINT`
- `README.md` : more documentation
- minor internal refactoring
- minor changes on `RipooException.php`, `ResponseFaultException`, `ResponseStatusException`

### Fixed
- no return class type on `OdooClient::getCurrentRipcordClient()` to suppress warning on PHP 7.2 for child Service classes
