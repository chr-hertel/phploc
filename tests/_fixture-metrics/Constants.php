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

define('FIRST_GLOBAL_CONSTANT', 1);

const SECOND_GLOBAL_CONSTANT = 2;

class ExampleConstants
{
    public const FIRST = 1;

    public const SECOND = 2, THIRD = 3;

    protected const FOURTH = 4;

    private const FIFTH = 5;
}
