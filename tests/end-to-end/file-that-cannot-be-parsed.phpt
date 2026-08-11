--TEST--
phploc ../_fixture-invalid
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../vendor/autoload.php';

$_SERVER['argv'][] = __DIR__ . '/../_fixture-invalid';

(new SebastianBergmann\PHPLOC\Application)->run($_SERVER['argv']);
--EXPECTF--
phploc %s by Sebastian Bergmann.

Directories:                                          1
Files:                                                1

Lines of Code (LOC):                                  0
Comment Lines of Code (CLOC):                         0 (0.00%)
Non-Comment Lines of Code (NCLOC):                    0 (0.00%)
Logical Lines of Code (LLOC):                         0 (0.00%)

Errors:
  Cannot parse %s/InvalidClass.php: Syntax error%s
