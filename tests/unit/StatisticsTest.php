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

#[CoversClass(Statistics::class)]
#[Small]
final class StatisticsTest extends TestCase
{
    public function testHasMinimumAverageAndMaximum(): void
    {
        $statistics = new Statistics(1, 2.5, 4);

        $this->assertSame(1, $statistics->minimum());
        $this->assertSame(2.5, $statistics->average());
        $this->assertSame(4, $statistics->maximum());
    }

    public function testIsCalculatedFromValues(): void
    {
        $statistics = Statistics::fromValues([4, 1, 3, 4]);

        $this->assertSame(1, $statistics->minimum());
        $this->assertSame(3.0, $statistics->average());
        $this->assertSame(4, $statistics->maximum());
    }

    public function testIsZeroWhenThereAreNoValues(): void
    {
        $statistics = Statistics::fromValues([]);

        $this->assertSame(0, $statistics->minimum());
        $this->assertSame(0.0, $statistics->average());
        $this->assertSame(0, $statistics->maximum());
    }
}
