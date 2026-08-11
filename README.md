**This project is no longer maintained and its repository is only kept for archival purposes.**

# PHPLOC

`phploc` is a tool for quickly measuring the size of a PHP project.

## Installation

This tool is installed using [Composer](https://getcomposer.org/):

```bash
$ composer require --dev phploc/phploc

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
