# Changes in PHPLOC

All notable changes in PHPLOC are documented in this file using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

Everything up to and including [7.0.2](https://github.com/sebastianbergmann/phploc/releases/tag/7.0.2) was released by Sebastian Bergmann as `phploc/phploc`; its history is documented in the [original repository](https://github.com/sebastianbergmann/phploc). Starting with 8.0.0, this changelog continues in the `hertel/phploc` fork.

## [8.0.0] - 202Y-MM-DD

### Added

* The `--json` CLI option prints the result as JSON instead of as text; it suppresses the version banner
* Files that cannot be read are now reported like files that cannot be parsed instead of aborting the run

### Changed

* Maintenance continues in the `hertel/phploc` fork of Sebastian Bergmann's `phploc/phploc`, under the original BSD-3-Clause license
* The namespace of this tool's classes was changed from `SebastianBergmann\PHPLOC` to `Hertel\PhpLoc` so that this package can be installed alongside `phploc/phploc` without duplicate class definitions
* The CLI is now built on `symfony/console`; `-v` is Symfony Console's verbosity option, the version is printed by `-V`/`--version`, and `--help` renders Symfony Console's help screen
* This tool is now installed using Composer instead of being distributed as a PHP Archive (PHAR)

### Removed

* The `--suffix` CLI option has been removed; only files with a name ending in `.php` are analysed
* The `--exclude` CLI option has been removed
* The `--debug` CLI option as well as the functionality it controlled has been removed; `Analyser::analyse()` no longer takes a `$debug` argument
* The `--count-tests` CLI option as well as the functionality it controlled has been removed
* The `--log-csv` CLI option as well as the functionality it controlled has been removed
* The `--log-json` CLI option as well as the functionality it controlled has been removed
* The `--log-xml` CLI option as well as the functionality it controlled has been removed
* This tool is no longer supported on PHP 7.3, PHP 7.4, PHP 8.0, PHP 8.1, PHP 8.2, and PHP 8.3

[8.0.0]: https://github.com/chr-hertel/phploc/compare/7.0.2...main
