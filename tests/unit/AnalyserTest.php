<?php declare(strict_types=1);
/*
 * This file is part of PHPLOC.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPLOC;

use const PHP_EOL;
use function ob_get_clean;
use function ob_start;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Analyser::class)]
#[UsesClass(Result::class)]
#[Small]
final class AnalyserTest extends TestCase
{
    public function testAnalysesFiles(): void
    {
        $result = (new Analyser)->analyse(
            [
                __DIR__ . '/../_fixture/example_function.php',
                __DIR__ . '/../_fixture/ExampleClass.php',
                __DIR__ . '/../_fixture/ExampleInterface.php',
                __DIR__ . '/../_fixture/ExampleTrait.php',
            ],
            false,
        );

        $this->assertFalse($result->hasErrors());
        $this->assertSame(1, $result->directories());
        $this->assertSame(4, $result->files());
        $this->assertSame(152, $result->linesOfCode());
        $this->assertSame(32, $result->commentLinesOfCode());
        $this->assertSame(120, $result->nonCommentLinesOfCode());
        $this->assertSame(40, $result->logicalLinesOfCode());
        $this->assertSame(1, $result->functions());
        $this->assertSame(2, $result->classesOrTraits());
        $this->assertSame(2, $result->methods());
    }

    public function testCountsDirectoriesThatContainAnalysedFiles(): void
    {
        $result = (new Analyser)->analyse(
            [
                __DIR__ . '/../_fixture/ExampleInterface.php',
                __DIR__ . '/../_fixture-single-line/single_line.php',
            ],
            false,
        );

        $this->assertSame(2, $result->directories());
        $this->assertSame(2, $result->files());
    }

    public function testCollectsErrorForFileThatCannotBeParsed(): void
    {
        $file = __DIR__ . '/../_fixture-invalid/InvalidClass.php';

        $result = (new Analyser)->analyse([$file], false);

        $this->assertTrue($result->hasErrors());
        $this->assertCount(1, $result->errors());
        $this->assertStringStartsWith('Cannot parse ' . $file . ':', $result->errors()[0]);
    }

    public function testCountsFileThatCannotBeParsedButDoesNotMeasureIt(): void
    {
        $result = (new Analyser)->analyse(
            [
                __DIR__ . '/../_fixture-invalid/InvalidClass.php',
                __DIR__ . '/../_fixture-single-line/single_line.php',
            ],
            false,
        );

        $this->assertSame(2, $result->files());
        $this->assertSame(1, $result->linesOfCode());
    }

    public function testAnalysesFileWithoutFunctionsOrMethods(): void
    {
        $result = (new Analyser)->analyse(
            [
                __DIR__ . '/../_fixture/ExampleInterface.php',
            ],
            false,
        );

        $this->assertSame(0, $result->functions());
        $this->assertSame(0, $result->methods());
        $this->assertSame(0, $result->classesOrTraits());
        $this->assertSame(0, $result->lowestCyclomaticComplexityForFunction());
        $this->assertSame(0.0, $result->averageCyclomaticComplexityForFunction());
        $this->assertSame(0, $result->highestCyclomaticComplexityForFunction());
        $this->assertSame(0, $result->lowestCyclomaticComplexityForMethod());
        $this->assertSame(0.0, $result->averageCyclomaticComplexityForMethod());
        $this->assertSame(0, $result->highestCyclomaticComplexityForMethod());
    }

    public function testAnalysesFileThatDoesNotEndWithNewline(): void
    {
        $result = (new Analyser)->analyse(
            [
                __DIR__ . '/../_fixture-single-line/single_line.php',
            ],
            false,
        );

        $this->assertSame(1, $result->linesOfCode());
        $this->assertSame(0, $result->commentLinesOfCode());
        $this->assertSame(1, $result->nonCommentLinesOfCode());
    }

    public function testAnalysesEmptyFile(): void
    {
        $result = (new Analyser)->analyse(
            [
                __DIR__ . '/../_fixture-empty/empty.php',
            ],
            false,
        );

        $this->assertFalse($result->hasErrors());
        $this->assertSame(1, $result->files());
        $this->assertSame(0, $result->linesOfCode());
        $this->assertSame(0, $result->commentLinesOfCode());
        $this->assertSame(0, $result->nonCommentLinesOfCode());
        $this->assertSame(0, $result->logicalLinesOfCode());
    }

    public function testPrintsNameOfAnalysedFileWhenDebugIsEnabled(): void
    {
        $file = __DIR__ . '/../_fixture-single-line/single_line.php';

        ob_start();

        (new Analyser)->analyse([$file], true);

        $output = ob_get_clean();

        $this->assertSame($file . PHP_EOL, $output);
    }
}
