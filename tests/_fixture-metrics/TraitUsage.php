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

trait ExampleUsedTrait
{
    public function traitMethod(): void
    {
        $value = 1;
    }
}

class ClassUsingTrait
{
    use ExampleUsedTrait;

    public function method(): void
    {
        $value = 2;
    }
}
