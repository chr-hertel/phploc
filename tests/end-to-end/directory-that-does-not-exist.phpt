--TEST--
phploc ../_fixture
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/_phploc.php';

phploc('does-not-exist');
--EXPECTF--
phploc %s by Sebastian Bergmann.

No files found to scan
