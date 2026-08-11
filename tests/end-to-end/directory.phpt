--TEST--
phploc ../_fixture
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/_phploc.php';

phploc(__DIR__ . '/../_fixture');
--EXPECTF--
phploc %s by Sebastian Bergmann.

Directories                                          1
Files                                                4

Size
  Lines of Code (LOC)                              152
  Comment Lines of Code (CLOC)                      32 (21.05%)
  Non-Comment Lines of Code (NCLOC)                120 (78.95%)
  Logical Lines of Code (LLOC)                      40 (26.32%)
    Classes                                         24 (60.00%)
      Average Class Length                          12
        Minimum Class Length                        12
        Maximum Class Length                        12
      Average Method Length                         12
        Minimum Method Length                       12
        Maximum Method Length                       12
      Average Methods Per Class                      2
        Minimum Methods Per Class                    1
        Maximum Methods Per Class                    2
    Functions                                       12 (30.00%)
      Average Function Length                       12
    Not in classes or functions                      4 (10.00%)

Cyclomatic Complexity
  Average Complexity per LLOC                     0.97
  Average Complexity per Class                   14.00
    Minimum Class Complexity                     14.00
    Maximum Class Complexity                     14.00
  Average Complexity per Method                  14.00
    Minimum Method Complexity                    14.00
    Maximum Method Complexity                    14.00
  Average Complexity per Function                14.00
    Minimum Function Complexity                  14.00
    Maximum Function Complexity                  14.00

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
  Interfaces                                         1
  Traits                                             1
  Enums                                              0
  Classes                                            1
    Abstract Classes                                 1 (100.00%)
    Concrete Classes                                 0 (0.00%)
      Final Classes                                  0 (0.00%)
      Non-Final Classes                              0 (0.00%)
  Methods                                            4
    Scope
      Non-Static Methods                             4 (100.00%)
      Static Methods                                 0 (0.00%)
    Visibility
      Public Methods                                 4 (100.00%)
      Protected Methods                              0 (0.00%)
      Private Methods                                0 (0.00%)
  Functions                                          1
    Named Functions                                  1 (100.00%)
    Anonymous Functions                              0 (0.00%)
  Constants                                          0
    Global Constants                                 0 (0.00%)
    Class Constants                                  0 (0.00%)
      Public Constants                               0 (0.00%)
      Non-Public Constants                           0 (0.00%)

Tests
  Classes                                            0
  Methods                                            0
