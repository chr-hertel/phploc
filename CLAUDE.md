# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project status

PHPLOC is **no longer maintained**; the repository is kept for archival purposes (see `README.md`). Prefer minimal, behavior-preserving changes unless the user explicitly asks otherwise.

## Tooling

All dev tools are PHIVE-managed PHARs committed to `tools/` — always invoke them through that path, never a globally installed binary:

```bash
./tools/composer update --no-interaction --no-progress   # install deps (no composer.lock is committed)
./tools/phpunit                                          # all tests
./tools/phpunit --testsuite unit                         # unit tests only
./tools/phpunit --testsuite end-to-end                   # .phpt CLI tests only
./tools/phpunit --filter testAnalysesFiles               # single test method
./tools/phpunit tests/unit/AnalyserTest.php              # single test file
./tools/php-cs-fixer fix                                 # apply coding standard
./tools/php-cs-fixer fix --dry-run --diff                # check only (what CI runs)
./tools/phpstan analyse                                  # static analysis (level max over src)
./tools/phpunit --coverage-clover=coverage.xml           # coverage (needs pcov/xdebug)
```

Ant targets in `build.xml` wrap the same things: `ant setup`, `ant test`, `ant phar` (builds `build/phploc-<version>.phar` via `tools/phpab`), `ant signed-phar`, `ant update-tools`.

Run the tool locally with `./phploc <directory>` after `composer update`.

CI (`.github/workflows/ci.yml`) runs php-cs-fixer (dry-run), PHPStan, and PHPUnit on PHP 8.4/8.5. `COMPOSER_ROOT_VERSION=8.0-dev` is set for Composer runs.

### Local toolchain

The tools require PHP >= 8.4, which matches the project's own requirement. Both interpreters on this machine work — the default `php` (8.5) and `/usr/bin/php8.4` — so every command above can be run as written. Coverage needs `XDEBUG_MODE=coverage php tools/phpunit --coverage-text`, because Xdebug is only installed for 8.5.

## Architecture

Single-purpose CLI: parse args → enumerate files → parse each file with `nikic/php-parser` → aggregate metrics → format as text.

`Application::run()` (`src/CLI/Application.php`) is the whole control flow:

1. `ArgumentsBuilder` wraps `sebastian/cli-parser` and returns an immutable `Arguments` value object (`--suffix`, `--exclude`, `--debug`, `--help`, `--version`).
2. `SebastianBergmann\FileIterator\Facade` expands directories into a file list.
3. `Analyser::analyse()` (`src/Analyser.php`) is where all measurement happens. Per file it builds a `NodeTraverser` with four visitors — `NameResolver`, `ParentConnectingVisitor`, then `ComplexityCalculatingVisitor` (from `sebastian/complexity`) and `LineCountingVisitor` (from `sebastian/lines-of-code`). **All LOC and complexity numbers come from those two upstream libraries**, not from code in this repo; the `Analyser` only merges per-file results and derives aggregates (unique directories, unique class/trait names split out of `Class::method` complexity entries, min/avg/max complexity for functions vs. methods).
4. Files that cannot be read or parsed raise `ParserException` and are collected into `Result::errors()` rather than aborting the run. `TextResultFormatter` appends an `Errors:` section for them; the exit code stays `0`, and an unparseable file still counts toward `Files:` while contributing no metrics.
5. `Result` is a read-only value object with a fixed 16-argument constructor; `TextResultFormatter` renders it. Adding a metric means touching `Analyser`, `Result`'s constructor/accessors, `TextResultFormatter`, `tests/_expectations/result.txt`, and the `.phpt` expectations.

### Conventions

- **Namespaces are flat**: everything is `SebastianBergmann\PHPLOC`, including classes under `src/CLI/` and `src/Exception/`. Directories are organizational only — autoloading is a `classmap` over `src/`, so directory names carry no namespace meaning.
- All classes are `final`, files start with `<?php declare(strict_types=1);` followed by the standard license header, and functions/constants are imported with `use function` / `use const` (php-cs-fixer enforces this).
- Exceptions extend `RuntimeException` and implement the marker interface `Exception`.
- PHPStan runs at `level: max` over `src` (config: `phpstan.neon.dist`) with **no baseline** — keep it that way; fix findings instead of ignoring them. Types like `list<non-empty-string>` and `non-negative-int` are expressed in plain `@param`/`@return`/`@var` docblocks (no vendor prefix) and paired with runtime `assert()` calls where a value's type is only promised by a docblock. `@throws` docblocks are maintained by hand — PHPStan does not verify them (Psalm's `checkForThrowsDocblock` has no equivalent).

### Tests

- `tests/unit/` — PHPUnit tests using attributes (`#[CoversClass]`, `#[UsesClass]`, `#[Small]`). `phpunit.xml` sets `requireCoverageMetadata` and `beStrictAboutCoverageMetadata`, so every test class needs coverage attributes or the suite fails.
- `tests/end-to-end/*.phpt` — invoke `Application::run()` with a crafted `$_SERVER['argv']` and assert on full CLI output via `--EXPECTF--`. PHPUnit's `.phpt` runner **silently ignores `--EXPECT_EXITCODE--`** (a wrong value still passes), so assert exit codes in `ApplicationTest` against `Application::run()`'s return value instead.
- `tests/_fixture/` is analysed by both suites; its hard-coded expected numbers (152 LOC, 4 files, complexity 14, …) appear in `AnalyserTest`, `tests/end-to-end/directory.phpt`, and the other `.phpt` files. **Editing any fixture file breaks all of them at once** — update every expectation together. For the same reason, **never add a file to `tests/_fixture/` or a subdirectory of it**: the `.phpt` tests scan that directory recursively, so a new file shifts every count. Put new fixtures in a sibling directory instead — `tests/_fixture-invalid/` (unparseable source), `tests/_fixture-single-line/` (no trailing newline), and `tests/_fixture-empty/` (zero bytes) follow that convention.
- Expected formatter output lives in `tests/_expectations/*.txt`, compared with `assertStringEqualsFile`. The four files cover the report's shapes: full, classes-but-no-functions, neither, and with errors.
- `tests/_fixture-invalid/InvalidClass.php` deliberately contains a syntax error. Nothing lints it — php-cs-fixer's finder covers only `src` and `tests/unit`, PHPStan covers only `src`, and the Composer classmap covers only `src`.
