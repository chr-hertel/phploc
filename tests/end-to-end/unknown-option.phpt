--TEST--
phploc --unknown ../_fixture
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../vendor/autoload.php';

$_SERVER['argv'][] = '--unknown';
$_SERVER['argv'][] = __DIR__ . '/../_fixture';

(new SebastianBergmann\PHPLOC\Application)->run($_SERVER['argv']);
--EXPECTF--
phploc %s by Sebastian Bergmann.

Unknown option "--unknown". Most similar options are %s
