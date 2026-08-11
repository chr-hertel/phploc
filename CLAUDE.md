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
./tools/psalm --config=.psalm/config.xml                 # static analysis
./tools/phpunit --coverage-clover=coverage.xml           # coverage (needs pcov/xdebug)
```

Ant targets in `build.xml` wrap the same things: `ant setup`, `ant test`, `ant phar` (builds `build/phploc-<version>.phar` via `tools/phpab`), `ant signed-phar`, `ant update-tools`.

Run the tool locally with `./phploc <directory>` after `composer update`.

CI (`.github/workflows/ci.yml`) runs php-cs-fixer (dry-run), Psalm, and PHPUnit on PHP 8.2/8.3/8.4. `COMPOSER_ROOT_VERSION=8.0-dev` is set for Composer runs.

### Local toolchain caveats

The pinned tools predate the PHP versions installed on this machine, so pick the interpreter per task:

| Task | Command | Why |
|---|---|---|
| Tests | `/usr/bin/php8.4 tools/phpunit` | all green |
| Coverage | `XDEBUG_MODE=coverage php tools/phpunit --path-coverage --coverage-text` | Xdebug is only installed for 8.5 |
| php-cs-fixer | `PHP_CS_FIXER_IGNORE_ENV=1 /usr/bin/php8.4 tools/php-cs-fixer fix` | 3.37.1 refuses PHP > 8.2 |
| Psalm | not runnable locally | 5.15 requires PHP ≤ 8.3; CI runs it on 8.2 |

Default `php` is 8.5, where **all `.phpt` tests fail spuriously**: PHPUnit 10.4.2 passes `-d report_memleaks=0` to every `.phpt` subprocess, PHP 8.5 deprecated that directive, and the resulting startup notice lands in the captured output ahead of the `--EXPECTF--` block. A toolchain mismatch, not a repo bug. Coverage is still measured correctly on 8.5 (failing tests execute), so the split above is workable: verify with 8.4, measure with 8.5.

## Architecture

Single-purpose CLI: parse args → enumerate files → parse each file with `nikic/php-parser` → aggregate metrics → format as text.

`Application::run()` (`src/CLI/Application.php`) is the whole control flow:

1. `ArgumentsBuilder` wraps `sebastian/cli-parser` and returns an immutable `Arguments` value object (`--suffix`, `--exclude`, `--debug`, `--help`, `--version`).
2. `SebastianBergmann\FileIterator\Facade` expands directories into a file list.
3. `Analyser::analyse()` (`src/Analyser.php`) is where all measurement happens. Per file it builds a `NodeTraverser` with four visitors — `NameResolver`, `ParentConnectingVisitor`, then `ComplexityCalculatingVisitor` (from `sebastian/complexity`) and `LineCountingVisitor` (from `sebastian/lines-of-code`). **All LOC and complexity numbers come from those two upstream libraries**, not from code in this repo; the `Analyser` only merges per-file results and derives aggregates (unique directories, unique class/trait names split out of `Class::method` complexity entries, min/avg/max complexity for functions vs. methods).
4. Parse failures raise `ParserException` and are collected into `Result::errors()` rather than aborting the run. `TextResultFormatter` appends an `Errors:` section for them; the exit code stays `0`, and an unparseable file still counts toward `Files:` while contributing no metrics.
5. `Result` is a read-only value object with a fixed 16-argument constructor; `TextResultFormatter` renders it. Adding a metric means touching `Analyser`, `Result`'s constructor/accessors, `TextResultFormatter`, `tests/_expectations/result.txt`, and the `.phpt` expectations.

### Conventions

- **Namespaces are flat**: everything is `SebastianBergmann\PHPLOC`, including classes under `src/CLI/` and `src/Exception/`. Directories are organizational only — autoloading is a `classmap` over `src/`, so directory names carry no namespace meaning.
- All classes are `final`, files start with `<?php declare(strict_types=1);` followed by the standard license header, and functions/constants are imported with `use function` / `use const` (php-cs-fixer enforces this).
- Exceptions extend `RuntimeException` and implement the marker interface `Exception`.
- Psalm runs with `checkForThrowsDocblock="true"` and a baseline at `.psalm/baseline.xml` — new code needs accurate `@throws` docblocks; `@psalm-param`/`@psalm-return` with types like `list<non-empty-string>` and `non-negative-int` are used throughout and paired with runtime `assert()` calls.

### Tests

- `tests/unit/` — PHPUnit tests using attributes (`#[CoversClass]`, `#[UsesClass]`, `#[Small]`). `phpunit.xml` sets `requireCoverageMetadata` and `beStrictAboutCoverageMetadata`, so every test class needs coverage attributes or the suite fails.
- `tests/end-to-end/*.phpt` — invoke `Application::run()` with a crafted `$_SERVER['argv']` and assert on full CLI output via `--EXPECTF--`. PHPUnit's `.phpt` runner **silently ignores `--EXPECT_EXITCODE--`** (a wrong value still passes), so assert exit codes in `ApplicationTest` against `Application::run()`'s return value instead.
- `tests/_fixture/` is analysed by both suites; its hard-coded expected numbers (152 LOC, 4 files, complexity 14, …) appear in `AnalyserTest`, `tests/end-to-end/directory.phpt`, and the other `.phpt` files. **Editing any fixture file breaks all of them at once** — update every expectation together. For the same reason, **never add a file to `tests/_fixture/` or a subdirectory of it**: the `.phpt` tests scan that directory recursively, so a new file shifts every count. Put new fixtures in a sibling directory instead — `tests/_fixture-invalid/` (unparseable source) and `tests/_fixture-single-line/` (no trailing newline) follow that convention.
- Expected formatter output lives in `tests/_expectations/*.txt`, compared with `assertStringEqualsFile`. The four files cover the report's shapes: full, classes-but-no-functions, neither, and with errors.
- `tests/_fixture-invalid/InvalidClass.php` deliberately contains a syntax error. Nothing lints it — php-cs-fixer's finder covers only `src` and `tests/unit`, Psalm covers only `src`, and the Composer classmap covers only `src`.
