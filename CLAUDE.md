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
/usr/bin/php8.4 ./vendor/bin/php-cs-fixer fix                  # apply coding standard -- PHP 8.4, see below
/usr/bin/php8.4 ./vendor/bin/php-cs-fixer fix --dry-run --diff # check only (what CI runs)
./vendor/bin/phpstan analyse                   # static analysis (level max over src + phploc)
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text
```

`COMPOSER_ROOT_VERSION=8.0-dev` is needed for Composer runs because no version is tagged.

Run the tool locally with `./phploc <directory>` after `composer update`.

CI (`.github/workflows/ci.yml`) runs php-cs-fixer (dry-run), PHPStan, and PHPUnit on PHP 8.4/8.5.

### Local toolchain

Everything requires PHP >= 8.4.1, which matches the project's own requirement. Both interpreters on this machine work — the default `php` (8.5) and `/usr/bin/php8.4`. Coverage needs the `XDEBUG_MODE=coverage` prefix and the default `php`, because Xdebug is only installed for 8.5.

**php-cs-fixer must be run with `/usr/bin/php8.4`.** The `@Symfony:risky` ruleset configures `native_function_invocation` with the `@compiler_optimized` set, and which functions that covers is derived from the PHP version the fixer itself runs on: PHP 8.5 wants `\escapeshellarg(...)`/`\exec(...)`/`\implode(...)`, PHP 8.4 wants them unprefixed. The two cannot both be satisfied, and CI runs the fixer on 8.4, so 8.4 decides. The fixer prints a warning about this when it is run on a newer PHP than the project's minimum — that warning is the symptom, and a green local run on 8.5 that fails CI is the consequence. Delete `.php-cs-fixer.cache` when switching interpreters, or the cache hides the difference.

## Architecture

Single-purpose CLI: parse args → enumerate files → parse each file with `nikic/php-parser` → aggregate metrics → format as text.

The **`phploc` script in the repository root is the whole control flow** — it is not a thin wrapper. It locates the Composer autoloader (guarded by `class_exists(Analyser::class)`, so the PHAR-less script also works when included by an already-bootstrapped process) and then builds a `Symfony\Component\Console\SingleCommandApplication` inline: argument/option definitions plus a `setCode()` closure. `src/` holds only the measurement and formatting classes; there is deliberately **no `Application`/`Command` class** and no `src/CLI/` directory.

1. Symfony Console parses `--json`, `--help`, `--version` and the variadic `directory` argument. The suffix is hard-wired to `.php` and nothing is excluded; `--suffix`, `--exclude`, and `--debug` were removed as unused.
2. `SebastianBergmann\FileIterator\Facade` expands directories into a file list.
3. `Analyser::analyse(array $files)` (`src/Analyser.php`) drives the measurement. Per file it builds a `NodeTraverser` with `NameResolver`, `LineCountingVisitor` (from `sebastian/lines-of-code`), and this repo's `MetricsVisitor`.
   - **LOC, CLOC, and NCLOC come from `sebastian/lines-of-code`.** Everything else is measured by `MetricsVisitor`, which reports into a `Collector` shared by all files of a run; `Collector::result()` turns what was collected into the `Result`.
   - **Cyclomatic complexity is never counted by hand**: `MetricsVisitor::cyclomaticComplexity()` runs `sebastian/complexity`'s `CyclomaticComplexityCalculatingVisitor` in a sub-traversal over a class, a method body, a function body, or the whole file, so the definition of a decision point always stays that library's. `sebastian/complexity`'s higher-level `ComplexityCalculatingVisitor` is deliberately *not* used any more, because it cannot attribute complexity to a class or exclude a test class.
   - LLOC is **not** taken from `sebastian/lines-of-code`; `MetricsVisitor` attributes every line that holds an expression to the innermost scope that contains it (a class, a method, a function, or none of them) and `Size::logicalLinesOfCode()` is the sum of the three buckets, so they always add up. The definition is the same as the library's — a unique line holding a `PhpParser\Node\Expr` — so the totals agree, except that lines of an excluded test class are left out.
4. Test classes are **always** counted separately rather than measured, so `Analyser::detectTests()` runs on every invocation: it **parses files a second time** beforehand, with `TestDetectionVisitor`, to build the `TestClassRegistry`. A class may extend a base class declared in a file analysed later, so test classes cannot be recognised in a single pass. `MetricsVisitor` then skips a test class entirely (via `$testClassDepth`, **not** via `DONT_TRAVERSE_CHILDREN`, which would also stop `LineCountingVisitor` from seeing its comments) and only counts it in the `Tests` section. Pest has no test classes, so a whole file counts as one.
   - Because that pass is no longer opt-in, it is kept cheap: `TestClassRegistry::couldDeclareTests()` rules a file out from its raw source before it is parsed (parsing is nearly the whole cost of the pass), and `TestDetectionVisitor` never descends into a class-like or function body. Together these keep the overhead at roughly 15% of a run instead of 50%. **`couldDeclareTests()` mirrors the rules in `isTestClass()` and `declaresPestTests()` and has to be kept in sync with them** — it lives in `TestClassRegistry` for that reason, and `TestClassRegistryTest` pins down what it must let through. Ruling a file out wrongly would silently measure a test class as production code.
5. Files that cannot be read or parsed raise `ParserException` and are collected into `Result::errors()` rather than aborting the run. `TextResultFormatter` appends an `Errors:` section for them; the exit code stays `0`, and an unparseable file still counts toward `Files:` while contributing no metrics. The test-detection pass ignores such files silently — the measuring pass reports them.
6. `Result` is a read-only value object composed of `Size`, `Complexity`, `Dependencies`, `Structure`, and `Tests`, all of them read-only too; `Statistics` (minimum/average/maximum) is shared by the length and complexity metrics. `TextResultFormatter` renders the report in Sebastian Bergmann's original four-section layout and `JsonResultFormatter` renders JSON for `--json`. Adding a metric means touching `MetricsVisitor`, `Collector`, the value object of its section, **both** formatters, every `tests/_expectations/*` file, and the `.phpt` expectations.

Both formatters always emit every metric, the `Tests` section included, so consumers can rely on a stable shape. `--json` also suppresses the version banner, since it would make the output invalid JSON.

### Metric semantics worth knowing

These are deliberate decisions, not accidents; several of them differ from the original token-based implementation, which could not see the syntax tree:

- Enums are reported separately from classes but are measured like a class (methods, constants, length, complexity). Interfaces contribute their logical lines but are left out of the class length/complexity/methods-per-class statistics, because they have no implementation.
- Anonymous classes count as concrete, non-final classes and open a scope of their own; arrow functions count as anonymous functions.
- `Structure` counts methods as they are declared, so abstract and interface methods are included; the complexity and length statistics only cover methods that have a body.
- `declare(strict_types=1);` is a logical line, because its `1` is an expression — this matches `sebastian/lines-of-code`. `namespace`, `use`, and `use SomeTrait;` are not.
- A global constant access is only counted when the constant is defined by the analysed code itself (via `define()` or a namespace-level `const`), matched on the short name. Constants of PHP or of dependencies are not dependencies of the analysed code.

### Conventions

- **Namespaces follow the directory tree**, enforced by PSR-4 autoloading of `Hertel\PhpLoc\` from `src/`, so a class cannot be filed in one place and named for another. Four groups sit under the root namespace, which holds only the `Analyser` entry point and the `Result` it returns:
  - `Hertel\PhpLoc\Metric\` — the read-only value objects a `Result` is made of, one per section of the report, plus the shared `Statistics`
  - `Hertel\PhpLoc\Measurement\` — the mutable machinery that produces those numbers: `Collector`, the two visitors, `Scope`, `TestClassRegistry`, `Visibility`. All `@internal`
  - `Hertel\PhpLoc\Formatter\` — the two formatters
  - `Hertel\PhpLoc\Exception\` — the marker interface and `ParserException`
  The tests mirror this: `tests/unit/Metric/`, `tests/unit/Formatter/`, `tests/unit/Measurement/`, each in the matching namespace. `AnalyserTest` stays at the root, because it covers the whole measuring pipeline across `Measurement\`.
- The namespace was renamed from `SebastianBergmann\PHPLOC` when the fork was adopted, so that installing this package alongside the original `phploc/phploc` cannot produce duplicate class definitions; the per-file license headers still credit Sebastian Bergmann and stay unchanged.
- All classes are `final`, and files start with a bare `<?php`, a blank line, and the standard license header. The `@Symfony:risky` ruleset's `native_function_invocation` writes `\count(...)`/`\sprintf(...)` for compiler-optimized functions but leaves `min`/`max`/`array_sum` unprefixed — run the fixer instead of guessing.
- The value objects in `Metric\` are `final readonly` with promoted, private constructor properties and accessor methods.
- Test helper methods must not collide with `PHPUnit\Framework\TestCase`'s **final** methods — `result()`, `size()`, and others are taken, which is why the builders in the tests are named `createResult()`, `createSize()`, and so on. A collision is a fatal error, not a test failure.
- Exceptions extend `RuntimeException` and implement the marker interface `Exception`.
- PHPStan runs at `level: 8` over `src` **and the extensionless `phploc` script** (config: `phpstan.neon.dist`) with **no baseline** — keep the baseline empty; fix findings instead of ignoring them. Level 8 rather than max is a deliberate choice: level 9 rejects passing Symfony Console's `mixed` accessors into `FileIterator\Facade`, which forces a narrowing helper into the `phploc` script. At level 8 the script passes `$input->getArgument('directory')` straight through. Types like `list<non-empty-string>` and `non-negative-int` are expressed in plain `@param`/`@return`/`@var` docblocks (no vendor prefix) and paired with runtime `assert()` calls where a value's type is only promised by a docblock. `@throws` docblocks are maintained by hand — PHPStan does not verify them (Psalm's `checkForThrowsDocblock` has no equivalent).

### Tests

- `tests/unit/` — PHPUnit tests using attributes (`#[CoversClass]`, `#[UsesClass]`, `#[Small]`). `phpunit.xml` sets `requireCoverageMetadata` and `beStrictAboutCoverageMetadata`, so every test class needs coverage attributes or the suite fails.
- `tests/end-to-end/*.phpt` — run the `phploc` script in a subprocess via the `phploc()` helper in `tests/end-to-end/_phploc.php` (which merges STDERR into STDOUT, because Symfony Console writes errors to STDERR) and assert on full CLI output via `--EXPECTF--`. PHPUnit's `.phpt` runner **silently ignores `--EXPECT_EXITCODE--`** (a wrong value still passes), so exit codes are asserted in `tests/unit/ExitCodeTest.php`, which shells out the same way. Since the CLI lives in the `phploc` script rather than in an autoloaded class, neither suite can measure code coverage for it.
- `tests/_fixture/` is analysed by both suites; its hard-coded expected numbers (152 LOC, 4 files, complexity 14, …) appear in `AnalyserTest`, `tests/end-to-end/directory.phpt`, and the other `.phpt` files. **Editing any fixture file breaks all of them at once** — update every expectation together. For the same reason, **never add a `.php` file to `tests/_fixture/` or a subdirectory of it**: the `.phpt` tests scan that directory recursively, so a new file shifts every count. Put new fixtures in a sibling directory instead — `tests/_fixture-invalid/` (unparseable source), `tests/_fixture-single-line/` (no trailing newline), `tests/_fixture-empty/` (zero bytes), `tests/_fixture-metrics/` (one file per group of detailed metrics, each analysed on its own by `AnalyserTest`), and `tests/_fixture-tests/` (test classes of several frameworks plus one Pest file and one production class, analysed as a directory by `tests/end-to-end/directory-with-tests.phpt`) follow that convention.
- Expected formatter output lives in `tests/_expectations/*.txt` and `*.json`, compared with `assertStringEqualsFile`, and is built from `Hertel\PhpLoc\ResultFixture` in `tests/_helper/` (autoloaded through composer's `autoload-dev` classmap — it stays a classmap rather than PSR-4 because `tests/` does not mirror the namespace, and PHPUnit only includes files whose name ends with `Test.php`). Every metric in that fixture has a distinct value so that a formatter mixing two of them up cannot go unnoticed. The variants cover the report's shapes: plain, with errors, with tests, and all-zero.
- `tests/_fixture-invalid/InvalidClass.php` deliberately contains a syntax error. Nothing lints it — php-cs-fixer's finder covers `src`, `tests/_helper`, `tests/unit`, and the `phploc` script, PHPStan covers `src` and `phploc`, and Composer autoloads only `src` (PSR-4) and `tests/_helper` (classmap).
