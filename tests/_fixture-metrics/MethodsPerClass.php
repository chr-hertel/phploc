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

class ClassWithoutMethods
{
}

class ClassWithTwoMethods
{
    public function one(): void
    {
        $value = 1;
    }

    public function two(): void
    {
        $value = 1;
        $value = 2;
    }
}

class ClassWithFourMethods
{
    public function one(): void
    {
    }

    public function two(): void
    {
    }

    public function three(): void
    {
    }

    public function four(): void
    {
    }
}
