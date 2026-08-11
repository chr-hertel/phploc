<?php

/*
 * This file is part of PHPLOC.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hertel\PhpLoc;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[CoversClass(Tests::class)]
#[Small]
final class TestsTest extends TestCase
{
    public function testHasClassesAndMethods(): void
    {
        $tests = new Tests(3, 12);

        $this->assertSame(3, $tests->classes());
        $this->assertSame(12, $tests->methods());
    }
}
