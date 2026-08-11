<?php

/*
 * This file is part of PHPLOC.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hertel\PhpLoc\Metric;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Size::class)]
#[UsesClass(Statistics::class)]
#[Small]
final class SizeTest extends TestCase
{
    public function testHasLinesOfCode(): void
    {
        $size = $this->createSize();

        $this->assertSame(100, $size->linesOfCode());
        $this->assertSame(20, $size->commentLinesOfCode());
        $this->assertSame(80, $size->nonCommentLinesOfCode());
        $this->assertSame(50, $size->logicalLinesOfCode());
    }

    public function testHasPercentagesOfLinesOfCode(): void
    {
        $size = $this->createSize();

        $this->assertSame(20.0, $size->commentLinesOfCodePercentage());
        $this->assertSame(80.0, $size->nonCommentLinesOfCodePercentage());
        $this->assertSame(50.0, $size->logicalLinesOfCodePercentage());
    }

    public function testHasLogicalLinesOfCodePerScope(): void
    {
        $size = $this->createSize();

        $this->assertSame(30, $size->logicalLinesInClasses());
        $this->assertSame(15, $size->logicalLinesInFunctions());
        $this->assertSame(5, $size->logicalLinesNotInClassesOrFunctions());
    }

    public function testHasPercentagesOfLogicalLinesOfCodePerScope(): void
    {
        $size = $this->createSize();

        $this->assertSame(60.0, $size->logicalLinesInClassesPercentage());
        $this->assertSame(30.0, $size->logicalLinesInFunctionsPercentage());
        $this->assertSame(10.0, $size->logicalLinesNotInClassesOrFunctionsPercentage());
    }

    public function testHasLengthStatistics(): void
    {
        $size = $this->createSize();

        $this->assertSame(2, $size->classLength()->minimum());
        $this->assertSame(4, $size->methodLength()->minimum());
        $this->assertSame(6, $size->methodsPerClass()->minimum());
        $this->assertSame(7.5, $size->averageFunctionLength());
    }

    public function testHasNoPercentagesWhenThereAreNoLines(): void
    {
        $size = new Size(
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            Statistics::fromValues([]),
            Statistics::fromValues([]),
            Statistics::fromValues([]),
            0.0,
        );

        $this->assertSame(0.0, $size->commentLinesOfCodePercentage());
        $this->assertSame(0.0, $size->nonCommentLinesOfCodePercentage());
        $this->assertSame(0.0, $size->logicalLinesOfCodePercentage());
        $this->assertSame(0.0, $size->logicalLinesInClassesPercentage());
        $this->assertSame(0.0, $size->logicalLinesInFunctionsPercentage());
        $this->assertSame(0.0, $size->logicalLinesNotInClassesOrFunctionsPercentage());
    }

    private function createSize(): Size
    {
        return new Size(
            100,
            20,
            80,
            50,
            30,
            15,
            5,
            new Statistics(2, 2.5, 3),
            new Statistics(4, 4.5, 5),
            new Statistics(6, 6.5, 7),
            7.5,
        );
    }
}
