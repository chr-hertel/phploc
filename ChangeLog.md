# Changes in PHPLOC

All notable changes in PHPLOC are documented in this file using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

Everything up to and including [7.0.2](https://github.com/sebastianbergmann/phploc/releases/tag/7.0.2) was released by Sebastian Bergmann as `phploc/phploc`; its history is documented in the [original repository](https://github.com/sebastianbergmann/phploc). Starting with 8.0.0, this changelog continues in the `hertel/phploc` fork.

## [8.0.0] - 202Y-MM-DD

### Added

* The `--json` CLI option prints the result as JSON instead of as text; it suppresses the version banner
* Files that cannot be read are now reported like files that cannot be parsed instead of aborting the run
* The detailed `Size`, `Cyclomatic Complexity`, `Dependencies`, and `Structure` sections of the report are back; they are measured from the abstract syntax tree now instead of from the token stream
* `Size` reports the logical lines of code in classes, in functions, and outside of both, as well as average, minimum, and maximum class length, method length, and number of methods per class
* `Cyclomatic Complexity` reports the average complexity per logical line of code and the average, minimum, and maximum complexity per class; the average, minimum, and maximum complexity per function is new compared to the original report
* `Structure` reports enums, which the original report did not know about
* Test classes and test methods are reported in a `Tests` section of their own and are no longer measured as production code. This happens by default; the `--count-tests` option the original had is gone, because there is nothing left to switch on. Test classes of PHPUnit (including `PHPUnit\Framework\TestCase` and `#[Test]`), Codeception, and PhpSpec are recognised, as are Pest files, in which every file that declares tests counts as one test class and every `test()` / `it()` call as one test method

### Changed

* Maintenance continues in the `hertel/phploc` fork of Sebastian Bergmann's `phploc/phploc`, under the original BSD-3-Clause license
* The namespace of this tool's classes was changed from `SebastianBergmann\PHPLOC` to `Hertel\PhpLoc` so that this package can be installed alongside `phploc/phploc` without duplicate class definitions
* The classes are grouped into `Hertel\PhpLoc\Metric\` (the value objects a `Result` is made of), `Hertel\PhpLoc\Measurement\` (the machinery that produces them, all `@internal`), `Hertel\PhpLoc\Formatter\`, and `Hertel\PhpLoc\Exception\`; `Analyser` and `Result` remain in the root namespace. Autoloading is PSR-4 instead of a classmap, so the namespaces and the directory tree cannot drift apart
* The CLI is now built on `symfony/console`; `-v` is Symfony Console's verbosity option, the version is printed by `-V`/`--version`, and `--help` renders Symfony Console's help screen
* This tool is now installed using Composer instead of being distributed as a PHP Archive (PHAR)
* Anonymous classes are counted as concrete, non-final classes, and anonymous functions include arrow functions; the original token-based implementation counted neither
* Enums are reported separately and are not part of the class counts, but their methods, constants, and complexity are measured like those of a class
* Methods are counted as they are declared, so abstract and interface methods are part of `Structure`; the complexity and length statistics only cover methods that have a body
* `Result` is no longer a flat list of values: it is composed of a `Size`, `Complexity`, `Dependencies`, `Structure`, and a `Tests` value object
* The JSON output gained the new metrics; the `directories`, `files`, `linesOfCode`, `classesOrTraits`, `methods.count`, `methods.cyclomaticComplexity`, `functions.count`, `functions.cyclomaticComplexity`, and `errors` keys are still where they were, but `methods.count` now includes methods without a body and `functions.count` now includes anonymous functions
* The text report no longer leaves out sections when nothing was found; every metric is always printed

### Removed

* The `--suffix` CLI option has been removed; only files with a name ending in `.php` are analysed
* The `--exclude` CLI option has been removed
* The `--debug` CLI option as well as the functionality it controlled has been removed; `Analyser::analyse()` no longer takes a `$debug` argument
* The `--count-tests` CLI option has been removed; tests are counted unconditionally now
* The `--log-csv` CLI option as well as the functionality it controlled has been removed
* The `--log-json` CLI option as well as the functionality it controlled has been removed
* The `--log-xml` CLI option as well as the functionality it controlled has been removed
* This tool is no longer supported on PHP 7.3, PHP 7.4, PHP 8.0, PHP 8.1, PHP 8.2, and PHP 8.3

[8.0.0]: https://github.com/chr-hertel/phploc/compare/7.0.2...main
