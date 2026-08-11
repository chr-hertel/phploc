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

#[CoversClass(Result::class)]
#[UsesClass(Complexity::class)]
#[UsesClass(Dependencies::class)]
#[UsesClass(Size::class)]
#[UsesClass(Statistics::class)]
#[UsesClass(Structure::class)]
#[UsesClass(Tests::class)]
#[Small]
final class ResultTest extends TestCase
{
    public function testMayHaveNoErrors(): void
    {
        $result = $this->createResult();

        $this->assertFalse($result->hasErrors());
        $this->assertSame([], $result->errors());
    }

    public function testMayHaveErrors(): void
    {
        $result = $this->createResult(['error']);

        $this->assertTrue($result->hasErrors());
        $this->assertSame(['error'], $result->errors());
    }

    public function testHasDirectoriesAndFiles(): void
    {
        $result = $this->createResult();

        $this->assertSame(1, $result->directories());
        $this->assertSame(2, $result->files());
    }

    public function testHasTheSectionsOfTheReport(): void
    {
        $result = $this->createResult();

        $this->assertSame(10, $result->size()->linesOfCode());
        $this->assertSame(0.5, $result->complexity()->averagePerLogicalLine());
        $this->assertSame(1, $result->dependencies()->globalAccesses());
        $this->assertSame(2, $result->structure()->namespaces());
    }

    public function testHasTests(): void
    {
        $result = $this->createResult([], new Tests(3, 4));

        $this->assertSame(3, $result->tests()->classes());
        $this->assertSame(4, $result->tests()->methods());
    }

    /**
     * @param list<non-empty-string> $errors
     */
    private function createResult(array $errors = [], ?Tests $tests = null): Result
    {
        return new Result(
            $errors,
            1,
            2,
            new Size(10, 4, 6, 3, 2, 1, 0, new Statistics(1, 1.0, 1), new Statistics(1, 1.0, 1), new Statistics(1, 1.0, 1), 1.0),
            new Complexity(0.5, new Statistics(1, 1.0, 1), new Statistics(1, 1.0, 1), new Statistics(1, 1.0, 1)),
            new Dependencies(1, 0, 0, 0, 0, 0, 0),
            new Structure(2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
            $tests ?? new Tests(0, 0),
        );
    }
}
