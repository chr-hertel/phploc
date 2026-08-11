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

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ExampleTest extends TestCase
{
    public function testSomething(): void
    {
        $this->assertTrue(true);
    }

    #[Test]
    public function somethingElse(): void
    {
        $this->assertTrue(true);
    }

    private function helper(): bool
    {
        return true;
    }
}
