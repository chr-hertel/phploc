--TEST--
phploc ../_fixture-tests
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/_phploc.php';

phploc(__DIR__ . '/../_fixture-tests');
--EXPECTF--
phploc %s by Sebastian Bergmann.

Directories                                          1
Files                                                7

Size
  Lines of Code (LOC)                              160
  Comment Lines of Code (CLOC)                      62 (38.75%)
  Non-Comment Lines of Code (NCLOC)                 98 (61.25%)
  Logical Lines of Code (LLOC)                       1 (0.62%)
    Classes                                          1 (100.00%)
      Average Class Length                           1
        Minimum Class Length                         1
        Maximum Class Length                         1
      Average Method Length                          1
        Minimum Method Length                        1
        Maximum Method Length                        1
      Average Methods Per Class                      1
        Minimum Methods Per Class                    1
        Maximum Methods Per Class                    1
    Functions                                        0 (0.00%)
      Average Function Length                        0
    Not in classes or functions                      0 (0.00%)

Cyclomatic Complexity
  Average Complexity per LLOC                     0.00
  Average Complexity per Class                    1.00
    Minimum Class Complexity                      1.00
    Maximum Class Complexity                      1.00
  Average Complexity per Method                   1.00
    Minimum Method Complexity                     1.00
    Maximum Method Complexity                     1.00
  Average Complexity per Function                 0.00
    Minimum Function Complexity                   0.00
    Maximum Function Complexity                   0.00

Dependencies
  Global Accesses                                    0
    Global Constants                                 0 (0.00%)
    Global Variables                                 0 (0.00%)
    Super-Global Variables                           0 (0.00%)
  Attribute Accesses                                 0
    Non-Static                                       0 (0.00%)
    Static                                           0 (0.00%)
  Method Calls                                       0
    Non-Static                                       0 (0.00%)
    Static                                           0 (0.00%)

Structure
  Namespaces                                         1
  Interfaces                                         0
  Traits                                             0
  Enums                                              0
  Classes                                            1
    Abstract Classes                                 0 (0.00%)
    Concrete Classes                                 1 (100.00%)
      Final Classes                                  1 (100.00%)
      Non-Final Classes                              0 (0.00%)
  Methods                                            1
    Scope
      Non-Static Methods                             1 (100.00%)
      Static Methods                                 0 (0.00%)
    Visibility
      Public Methods                                 1 (100.00%)
      Protected Methods                              0 (0.00%)
      Private Methods                                0 (0.00%)
  Functions                                          0
    Named Functions                                  0 (0.00%)
    Anonymous Functions                              0 (0.00%)
  Constants                                          0
    Global Constants                                 0 (0.00%)
    Class Constants                                  0 (0.00%)
      Public Constants                               0 (0.00%)
      Non-Public Constants                           0 (0.00%)

Tests
  Classes                                            6
  Methods                                            7
