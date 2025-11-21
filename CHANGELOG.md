# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.1] - 2025-11-21

### Added
- Added `getErrMsg()` method to retrieve error messages
- Complete test suite for `JsonResp` class (29 tests)
- Improved test suite for `PasswordValidator` class with language and regex tests
- Added `.editorconfig` for code style consistency
- Added `.gitattributes` for Git handling
- Added Composer scripts: `test`, `test:coverage`, `test:coverage-html`
- Added keywords to composer.json for better discoverability
- Added this CHANGELOG.md file

### Changed
- Simplified `isSuccess()` and `isError()` methods
- Improved code formatting with consistent spacing
- Updated README with comprehensive examples and full documentation
- Updated composer.json description

### Fixed
- Added `JSON_THROW_ON_ERROR` flag to `json_encode()` for better error handling

## [1.0.0] - Initial Release

### Added
- `JsonResp` class for standardized JSON response formatting
- `PasswordValidator` class for password validation with preset configs
- Support for French and English error messages
- PHPUnit test suite
- GitHub Actions CI workflow
