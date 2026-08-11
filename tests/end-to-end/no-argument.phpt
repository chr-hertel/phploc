--TEST--
phploc
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/_phploc.php';

phploc();
--EXPECTF--
%ANot enough arguments (missing: "directory").%A
