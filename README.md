# PHPLOC

`phploc` is a tool for quickly measuring the size of a PHP project.

## About this fork

PHPLOC was created and maintained by [Sebastian Bergmann](https://github.com/sebastianbergmann) as [`phploc/phploc`](https://github.com/sebastianbergmann/phploc) from 2009 onwards, until he stopped maintaining it and kept the repository for archival purposes. Every idea, every metric, and nearly every line of the original implementation is his work, and this fork stands entirely on it. Thank you, Sebastian.

This fork exists for one reason: [OSS Complexity Report](https://christopher-hertel.de/oss-complexity-report/) runs `phploc` at its core, so the tool needs a maintained home. It is published as `hertel/phploc` and maintained by [Christopher Hertel](https://github.com/chr-hertel), under the original BSD-3-Clause license with the original copyright notice intact.

The measurements themselves are still not this project's own work: lines of code and cyclomatic complexity are calculated by Sebastian Bergmann's [`sebastian/lines-of-code`](https://github.com/sebastianbergmann/lines-of-code) and [`sebastian/complexity`](https://github.com/sebastianbergmann/complexity) libraries, and the parsing by [`nikic/php-parser`](https://github.com/nikic/PHP-Parser).

This fork is not affiliated with, supported by, or endorsed by Sebastian Bergmann. Please report issues with it [here](https://github.com/chr-hertel/phploc/issues), never to the original project.

## Installation

This tool is installed using [Composer](https://getcomposer.org/):

```bash
$ composer require --dev hertel/phploc

$ ./vendor/bin/phploc --version
```

## Usage Example

```
$ ./vendor/bin/phploc src
phploc 8.0-dev by Sebastian Bergmann.

Directories:                                        104
Files:                                              856

Lines of Code (LOC):                             67,955
Comment Lines of Code (CLOC):                    19,533 (28.74%)
Non-Comment Lines of Code (NCLOC):               48,422 (71.26%)
Logical Lines of Code (LLOC):                    18,478 (27.19%)

Classes or Traits                                   662
  Methods                                         3,389
    Cyclomatic Complexity
      Lowest                                       1.00
      Average                                      2.00
      Highest                                    156.00

Functions                                           185
  Cyclomatic Complexity
    Lowest                                         1.00
    Average                                        1.00
    Highest                                        1.00
```
