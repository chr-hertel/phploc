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

class EdgeCases
{
    public ?EdgeCases $attribute = null;

    public function method(string $name, callable $callable): void
    {
        // The name of a constant that is defined dynamically cannot be known
        define($name, 1);

        // The name of a variable variable cannot be known either
        $$name = 1;

        // A dynamic call is not a call to define()
        $callable();

        $value = $this->attribute?->attribute;
    }
}
