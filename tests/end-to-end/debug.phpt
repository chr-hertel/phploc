--TEST--
phploc --debug ../_fixture-single-line
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../vendor/autoload.php';

$_SERVER['argv'][] = '--debug';
$_SERVER['argv'][] = __DIR__ . '/../_fixture-single-line';

(new SebastianBergmann\PHPLOC\Application)->run($_SERVER['argv']);
--EXPECTF--
phploc %s by Sebastian Bergmann.

%s/single_line.php
Directories:                                          1
Files:                                                1

Lines of Code (LOC):                                  1
Comment Lines of Code (CLOC):                         0 (0.00%)
Non-Comment Lines of Code (NCLOC):                    1 (100.00%)
Logical Lines of Code (LLOC):                         1 (100.00%)
