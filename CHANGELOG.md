# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

## [1.6.2] - 2019-02-12

### Fixed
- `ModelHandlerTrait::search()`, `ModelHandlerTrait::search_read()` : parameter `$limit` set to default 0 (no limit) instead of `100`

## [1.6.1] - 2019-02-01

### Fixed
- Error introduced in `ModelHandlerTrait::search_read()`

## [1.6.0] - 2019-02-01

### Added
- method: `ModelHandlerTrait::model_execute_splat()`
- method: `OdooClient::getCurrentEndpoint()`
- require `ext-xmlrpc` in `composer.json`

## [1.5.1] - 2018-12-11

### Added
- file: `.editorconfig`

### Fixed
- Fix missing assignation of `$this->currentEndpoint` in `OdooClient::getService()` when service already set


## [1.5.0] - 2018-12-10

### Added
- method: `OdooClient::model_execute_kw()`
- method: `Service/****Service::getRawResponse()`
- method: `OdooClient::getArrayType()`
- method: `OdooClient::getResponseEntry()`
- method: `OdooClient::setResponse()`
- method: `OdooClient::isResponseSuccess()`
- method : `CommonHandlerTrait::tryAuthenticate()`
- classes: `PermissionException.php`, `ResponseEntryException.php`
- constants: `CommonHandlerTrait::VERSION_ENTRY_****`
- this `CHANGELOG.md`

### Changed
- renamed method: `OdooClient::formatResponse()` become `checkResponse()`
- renamed method: `OdooClient::getCurrentRipcordClient()` become `getCurrentService()`
- renamed method : `CommonHandlerTrait::testAuthenticate()` become `checkAuthenticate()` 
- method: `CommonHandlerTrait::version()` accepts an optional parameter to filter array output based on an entry
- constants: `OdooClient::ENDPOINT_****` now in `Service/****Service::ENPOINT`
- `README.md` : more documentation
- minor changes on `RipooException.php`, `ResponseFaultException`, `ResponseStatusException`
- removed try/catch in `ModelHandlerTrait::check_access_rights()`
- minor internal refactoring

### Fixed
- no return class type on `OdooClient::getCurrentRipcordClient()` to suppress warning on PHP 7.2 for child Service classes

