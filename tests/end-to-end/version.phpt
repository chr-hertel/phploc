--TEST--
phploc --version
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/_phploc.php';

phploc('--version');
--EXPECTF--
phploc %s
