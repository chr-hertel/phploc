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

function exampleNamedFunction(int $value): int
{
    if ($value > 0) {
        return $value;
    }

    return 0;
}

$exampleClosure = function (int $value): int {
    return $value > 0 ? $value : 0;
};

$exampleArrowFunction = fn (int $value): int => $value;
