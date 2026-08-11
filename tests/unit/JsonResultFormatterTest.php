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

#[CoversClass(JsonResultFormatter::class)]
#[UsesClass(Result::class)]
#[Small]
final class JsonResultFormatterTest extends TestCase
{
    public function testFormatsResultAsJson(): void
    {
        $this->assertStringEqualsFile(
            __DIR__.'/../_expectations/result.json',
            (new JsonResultFormatter())->format(
                new Result([], 1, 2, 10, 4, 6, 3, 7, 8, 9, 10, 11, 12, 13, 14, 15),
            ),
        );
    }

    public function testFormatsErrors(): void
    {
        $this->assertStringEqualsFile(
            __DIR__.'/../_expectations/result-with-errors.json',
            (new JsonResultFormatter())->format(
                new Result(
                    [
                        'Cannot parse /path/to/First.php: Syntax error',
                        'Cannot parse /path/to/Second.php: Syntax error',
                    ],
                    1,
                    2,
                    10,
                    4,
                    6,
                    3,
                    7,
                    8,
                    9,
                    10,
                    11,
                    12,
                    13,
                    14,
                    15,
                ),
            ),
        );
    }

    public function testFormatsAllKeysEvenWhenThereAreNoClassesOrFunctions(): void
    {
        $json = \json_decode(
            (new JsonResultFormatter())->format(
                new Result([], 1, 2, 10, 4, 6, 3, 0, 0, 0.0, 0, 0, 0, 0, 0.0, 0),
            ),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );

        $this->assertSame(
            ['directories', 'files', 'linesOfCode', 'classesOrTraits', 'methods', 'functions', 'errors'],
            \array_keys($json),
        );

        $this->assertSame(0, $json['functions']['count']);
        $this->assertSame(0.0, $json['functions']['cyclomaticComplexity']['average']);
        $this->assertSame(0, $json['methods']['count']);
        $this->assertSame(0.0, $json['methods']['cyclomaticComplexity']['average']);
    }
}
