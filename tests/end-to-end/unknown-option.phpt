--TEST--
phploc --unknown ../_fixture
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/_phploc.php';

phploc('--unknown', __DIR__ . '/../_fixture');
--EXPECTF--
%AThe "--unknown" option does not exist.%A
