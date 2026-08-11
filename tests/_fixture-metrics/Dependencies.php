<?php

/*
 * This file is part of PHPLOC.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hertel\PhpLoc\TestFixture;

define('FIRST_CONSTANT', 1);

const SECOND_CONSTANT = 2;

class Dependencies
{
    public static int $staticAttribute = 0;

    public int $attribute = 0;

    public function method(): void
    {
        $object = new self();

        $object->attribute = FIRST_CONSTANT;
        $object->attribute = SECOND_CONSTANT;
        self::$staticAttribute = $object->attribute;

        $object->method();
        $object?->method();
        self::staticMethod();

        global $globalVariable;

        $globalVariable = $GLOBALS['key'];
        $globalVariable = $_SERVER['key'];

        // PHP_EOL is not defined by the analysed code and is therefore not
        // counted as a dependency of it
        $globalVariable = \PHP_EOL;
    }

    public static function staticMethod(): void
    {
    }
}
