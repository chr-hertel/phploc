--TEST--
phploc --help
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/_phploc.php';

phploc('--help');
--EXPECTF--
Usage:
  %s [options] [--] <directory>...

Arguments:
  directory%w%A--suffix=SUFFIX%A--exclude=EXCLUDE%A--debug%A
