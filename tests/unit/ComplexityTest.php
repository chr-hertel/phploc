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
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Complexity::class)]
#[UsesClass(Statistics::class)]
#[Small]
final class ComplexityTest extends TestCase
{
    public function testHasAveragePerLogicalLine(): void
    {
        $this->assertSame(0.35, $this->createComplexity()->averagePerLogicalLine());
    }

    public function testHasStatisticsForClassesMethodsAndFunctions(): void
    {
        $complexity = $this->createComplexity();

        $this->assertSame(1, $complexity->classes()->minimum());
        $this->assertSame(5.2, $complexity->classes()->average());
        $this->assertSame(31, $complexity->classes()->maximum());

        $this->assertSame(2, $complexity->methods()->minimum());
        $this->assertSame(2.4, $complexity->methods()->average());
        $this->assertSame(12, $complexity->methods()->maximum());

        $this->assertSame(3, $complexity->functions()->minimum());
        $this->assertSame(3.75, $complexity->functions()->average());
        $this->assertSame(9, $complexity->functions()->maximum());
    }

    private function createComplexity(): Complexity
    {
        return new Complexity(
            0.35,
            new Statistics(1, 5.2, 31),
            new Statistics(2, 2.4, 12),
            new Statistics(3, 3.75, 9),
        );
    }
}
