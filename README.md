# PHPLOC

`phploc` is a tool for quickly measuring the size of a PHP project.

## About this fork

PHPLOC was created and maintained by [Sebastian Bergmann](https://github.com/sebastianbergmann) as
[`phploc/phploc`](https://github.com/sebastianbergmann/phploc) from 2009 onwards, until he stopped maintaining it and
kept the repository for archival purposes. Every idea, every metric, and nearly every line of the original
implementation is his work, and this fork stands entirely on it. Thank you, Sebastian.

This fork exists for one reason: [OSS Complexity Report](https://christopher-hertel.de/oss-complexity-report/) runs
`phploc` at its core, so the tool needs a maintained home. It is published as `hertel/phploc` and maintained by
[Christopher Hertel](https://github.com/chr-hertel), under the original BSD-3-Clause license with the original copyright
notice intact.

The measurements themselves are still not this project's own work: lines of code and cyclomatic complexity are
calculated by Sebastian Bergmann's [`sebastian/lines-of-code`](https://github.com/sebastianbergmann/lines-of-code) and
[`sebastian/complexity`](https://github.com/sebastianbergmann/complexity) libraries, and the parsing by
[`nikic/php-parser`](https://github.com/nikic/PHP-Parser).

This fork is not affiliated with, supported by, or endorsed by Sebastian Bergmann. Please report issues with it
[here](https://github.com/chr-hertel/phploc/issues), never to the original project.

## Installation

This tool is installed using [Composer](https://getcomposer.org/):

```bash
$ composer require --dev hertel/phploc

$ ./vendor/bin/phploc --version
```

Pass `--json` to get the result as JSON instead of as the text report, for example to feed it into another tool:

```bash
$ ./vendor/bin/phploc --json src
```

## Usage Example

```
$ ./vendor/bin/phploc src
phploc 8.0-dev by Sebastian Bergmann.

Directories                                          2
Files                                               18

Size
  Lines of Code (LOC)                            2,800
  Comment Lines of Code (CLOC)                     719 (25.68%)
  Non-Comment Lines of Code (NCLOC)              2,081 (74.32%)
  Logical Lines of Code (LLOC)                     873 (31.18%)
    Classes                                        873 (100.00%)
      Average Class Length                          51
        Minimum Class Length                         0
        Maximum Class Length                       189
      Average Method Length                          5
        Minimum Method Length                        1
        Maximum Method Length                       81
      Average Methods Per Class                     10
        Minimum Methods Per Class                    0
        Maximum Methods Per Class                   41
    Functions                                        0 (0.00%)
      Average Function Length                        0
    Not in classes or functions                      0 (0.00%)

Cyclomatic Complexity
  Average Complexity per LLOC                     0.15
  Average Complexity per Class                    8.65
    Minimum Class Complexity                      1.00
    Maximum Class Complexity                     66.00
  Average Complexity per Method                   1.74
    Minimum Method Complexity                     1.00
    Maximum Method Complexity                    19.00
  Average Complexity per Function                 0.00
    Minimum Function Complexity                   0.00
    Maximum Function Complexity                   0.00

Dependencies
  Global Accesses                                    0
    Global Constants                                 0 (0.00%)
    Global Variables                                 0 (0.00%)
    Super-Global Variables                           0 (0.00%)
  Attribute Accesses                               308
    Non-Static                                     308 (100.00%)
    Static                                           0 (0.00%)
  Method Calls                                     431
    Non-Static                                     418 (96.98%)
    Static                                          13 (3.02%)

Structure
  Namespaces                                         1
  Interfaces                                         1
  Traits                                             0
  Enums                                              1
  Classes                                           16
    Abstract Classes                                 0 (0.00%)
    Concrete Classes                                16 (100.00%)
      Final Classes                                 16 (100.00%)
      Non-Final Classes                              0 (0.00%)
  Methods                                          176
    Scope
      Non-Static Methods                           170 (96.59%)
      Static Methods                                 6 (3.41%)
    Visibility
      Public Methods                               144 (81.82%)
      Protected Methods                              0 (0.00%)
      Private Methods                               32 (18.18%)
  Functions                                          0
    Named Functions                                  0 (0.00%)
    Anonymous Functions                              0 (0.00%)
  Constants                                          2
    Global Constants                                 0 (0.00%)
    Class Constants                                  2 (100.00%)
      Public Constants                               0 (0.00%)
      Non-Public Constants                           2 (100.00%)

Tests
  Classes                                            0
  Methods                                            0
```

Test classes and test methods are always reported in the `Tests` section of their own instead of being measured as
production code, so pointing `phploc` at a whole project keeps the numbers about the production code:

```bash
$ ./vendor/bin/phploc src tests
```

Test classes of PHPUnit, Codeception, and PhpSpec are recognised, as are Pest files, in which every file that declares
tests counts as one test class and every `test()` / `it()` call as one test method.
