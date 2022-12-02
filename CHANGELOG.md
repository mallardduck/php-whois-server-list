# mallardduck/whois-server-list Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 2.0.0 - 2022-12-02
### Removed

- Support for PHP 7.x

### Changed

- Refactored how TLD whois server generator works
- Modified the underlying JSON file schema

### Fixed

- Bug related to parsing the PSL list causing domains to be missing.

## 1.1.0 - 2021-08-03

### Added

- Ability to look up IPv4 to find its whois server.

## 1.0.0 - 2021-07-29

### Added

- Release first stable version of new package.
