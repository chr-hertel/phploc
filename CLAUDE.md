# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project status

This repository is `hertel/phploc`, a **maintained fork** of Sebastian Bergmann's archived `phploc/phploc` (see `README.md`). It is maintained because [OSS Complexity Report](https://christopher-hertel.de/oss-complexity-report/) depends on it.

Attribution is deliberate and must be preserved: the BSD-3-Clause `LICENSE` keeps Sebastian Bergmann's original copyright notice alongside the fork's, the per-file license headers in `src/` are his and stay unchanged, and `README.md` credits the original project. Clause 3 of the license also means his name must not be used to endorse or promote this fork — describing where the code came from is fine, implying he backs it is not.

## Tooling

All dev tools are Composer `require-dev` dependencies invoked from `vendor/bin` — there is no `tools/` directory, no PHIVE, and no PHAR build:

```bash
COMPOSER_ROOT_VERSION=8.0-dev composer update  # install deps (no composer.lock is committed)
./vendor/bin/phpunit                           # all tests
./vendor/bin/phpunit --testsuite unit          # unit tests only
./vendor/bin/phpunit --testsuite end-to-end    # .phpt CLI tests only
./vendor/bin/phpunit --filter testAnalysesFiles
./vendor/bin/phpunit tests/unit/AnalyserTest.php
./vendor/bin/php-cs-fixer fix                  # apply coding standard
./vendor/bin/php-cs-fixer fix --dry-run --diff # check only (what CI runs)
./vendor/bin/phpstan analyse                   # static analysis (level max over src + phploc)
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text
```

`COMPOSER_ROOT_VERSION=8.0-dev` is needed for Composer runs because no version is tagged.

Run the tool locally with `./phploc <directory>` after `composer update`.

CI (`.github/workflows/ci.yml`) runs php-cs-fixer (dry-run), PHPStan, and PHPUnit on PHP 8.4/8.5.

### Local toolchain

Everything requires PHP >= 8.4.1, which matches the project's own requirement. Both interpreters on this machine work — the default `php` (8.5) and `/usr/bin/php8.4`. Coverage needs the `XDEBUG_MODE=coverage` prefix and the default `php`, because Xdebug is only installed for 8.5.

## Architecture

Single-purpose CLI: parse args → enumerate files → parse each file with `nikic/php-parser` → aggregate metrics → format as text.

The **`phploc` script in the repository root is the whole control flow** — it is not a thin wrapper. It locates the Composer autoloader (guarded by `class_exists(Analyser::class)`, so the PHAR-less script also works when included by an already-bootstrapped process) and then builds a `Symfony\Component\Console\SingleCommandApplication` inline: argument/option definitions plus a `setCode()` closure. `src/` holds only the measurement and formatting classes; there is deliberately **no `Application`/`Command` class** and no `src/CLI/` directory.

1. Symfony Console parses `--suffix`, `--exclude`, `--debug`, `--help`, `--version` and the variadic `directory` argument. Its accessors return `mixed`, so the `$listOfStrings` closure in the script narrows them to `list<non-empty-string>` with `assert()` — PHPStan runs over the script at level max, so this narrowing is load-bearing. `--suffix` **adds to** `.php` rather than replacing it, which is why the option is declared without a default.
2. `SebastianBergmann\FileIterator\Facade` expands directories into a file list.
3. `Analyser::analyse()` (`src/Analyser.php`) is where all measurement happens. Per file it builds a `NodeTraverser` with four visitors — `NameResolver`, `ParentConnectingVisitor`, then `ComplexityCalculatingVisitor` (from `sebastian/complexity`) and `LineCountingVisitor` (from `sebastian/lines-of-code`). **All LOC and complexity numbers come from those two upstream libraries**, not from code in this repo; the `Analyser` only merges per-file results and derives aggregates (unique directories, unique class/trait names split out of `Class::method` complexity entries, min/avg/max complexity for functions vs. methods).
4. Files that cannot be read or parsed raise `ParserException` and are collected into `Result::errors()` rather than aborting the run. `TextResultFormatter` appends an `Errors:` section for them; the exit code stays `0`, and an unparseable file still counts toward `Files:` while contributing no metrics.
5. `Result` is a read-only value object with a fixed 16-argument constructor; `TextResultFormatter` renders it as the human-readable report and `JsonResultFormatter` as JSON for `--json`. Adding a metric means touching `Analyser`, `Result`'s constructor/accessors, **both** formatters, `tests/_expectations/result.txt` and `result.json` (plus the `-with-errors` variants), and the `.phpt` expectations.

The two formatters differ on purpose: the text report omits whole sections when there are no classes or functions, while the JSON output always emits every key so that consumers can rely on a stable shape. `--json` also suppresses the version banner, since it would make the output invalid JSON; `--debug` still prints file names to STDOUT and therefore cannot be combined with `--json`.

### Conventions

- **Namespaces are flat**: everything is `Hertel\PhpLoc`, including classes under `src/Exception/`. Directories are organizational only — autoloading is a `classmap` over `src/`, so directory names carry no namespace meaning. The namespace was renamed from `SebastianBergmann\PHPLOC` when the fork was adopted, so that installing this package alongside the original `phploc/phploc` cannot produce duplicate class definitions; the per-file license headers still credit Sebastian Bergmann and stay unchanged.
- All classes are `final`, files start with `<?php declare(strict_types=1);` followed by the standard license header, and functions/constants are imported with `use function` / `use const` (php-cs-fixer enforces this). The `phploc` script is the exception: it lives in the global namespace, so php-cs-fixer's `native_function_invocation` rule writes `\assert(...)`/`\sprintf(...)` there instead.
- Exceptions extend `RuntimeException` and implement the marker interface `Exception`.
- PHPStan runs at `level: max` over `src` **and the extensionless `phploc` script** (config: `phpstan.neon.dist`) with **no baseline** — keep it that way; fix findings instead of ignoring them. Types like `list<non-empty-string>` and `non-negative-int` are expressed in plain `@param`/`@return`/`@var` docblocks (no vendor prefix) and paired with runtime `assert()` calls where a value's type is only promised by a docblock. `@throws` docblocks are maintained by hand — PHPStan does not verify them (Psalm's `checkForThrowsDocblock` has no equivalent).

### Tests

- `tests/unit/` — PHPUnit tests using attributes (`#[CoversClass]`, `#[UsesClass]`, `#[Small]`). `phpunit.xml` sets `requireCoverageMetadata` and `beStrictAboutCoverageMetadata`, so every test class needs coverage attributes or the suite fails.
- `tests/end-to-end/*.phpt` — run the `phploc` script in a subprocess via the `phploc()` helper in `tests/end-to-end/_phploc.php` (which merges STDERR into STDOUT, because Symfony Console writes errors to STDERR) and assert on full CLI output via `--EXPECTF--`. PHPUnit's `.phpt` runner **silently ignores `--EXPECT_EXITCODE--`** (a wrong value still passes), so exit codes are asserted in `tests/unit/ExitCodeTest.php`, which shells out the same way. Since the CLI lives in the `phploc` script rather than in an autoloaded class, neither suite can measure code coverage for it.
- `tests/_fixture/` is analysed by both suites; its hard-coded expected numbers (152 LOC, 4 files, complexity 14, …) appear in `AnalyserTest`, `tests/end-to-end/directory.phpt`, and the other `.phpt` files. **Editing any fixture file breaks all of them at once** — update every expectation together. For the same reason, **never add a file to `tests/_fixture/` or a subdirectory of it**: the `.phpt` tests scan that directory recursively, so a new file shifts every count. Put new fixtures in a sibling directory instead — `tests/_fixture-invalid/` (unparseable source), `tests/_fixture-single-line/` (no trailing newline), and `tests/_fixture-empty/` (zero bytes) follow that convention.
- Expected formatter output lives in `tests/_expectations/*.txt` and `*.json`, compared with `assertStringEqualsFile`. The four `.txt` files cover the report's shapes: full, classes-but-no-functions, neither, and with errors. The JSON output has no conditional sections, so two `.json` files (full, with errors) suffice; a third test decodes the output to assert the key set stays complete when every count is zero.
- `tests/_fixture-invalid/InvalidClass.php` deliberately contains a syntax error. Nothing lints it — php-cs-fixer's finder covers `src`, `tests/unit`, and the `phploc` script, PHPStan covers `src` and `phploc`, and the Composer classmap covers only `src`.
