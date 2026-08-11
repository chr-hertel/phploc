<?php

/*
 * This file is part of PHPLOC.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hertel\PhpLoc\Formatter;

use Hertel\PhpLoc\Metric\Complexity;
use Hertel\PhpLoc\Metric\Dependencies;
use Hertel\PhpLoc\Metric\Size;
use Hertel\PhpLoc\Metric\Statistics;
use Hertel\PhpLoc\Metric\Structure;
use Hertel\PhpLoc\Metric\Tests;
use Hertel\PhpLoc\Result;
use Hertel\PhpLoc\ResultFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(JsonResultFormatter::class)]
#[UsesClass(Complexity::class)]
#[UsesClass(Dependencies::class)]
#[UsesClass(Result::class)]
#[UsesClass(Size::class)]
#[UsesClass(Statistics::class)]
#[UsesClass(Structure::class)]
#[UsesClass(Tests::class)]
#[Small]
final class JsonResultFormatterTest extends TestCase
{
    public function testFormatsResultAsJson(): void
    {
        $this->assertStringEqualsFile(
            __DIR__.'/../../_expectations/result.json',
            (new JsonResultFormatter())->format(ResultFixture::result()),
        );
    }

    public function testFormatsErrors(): void
    {
        $this->assertStringEqualsFile(
            __DIR__.'/../../_expectations/result-with-errors.json',
            (new JsonResultFormatter())->format(ResultFixture::resultWithErrors()),
        );
    }

    public function testFormatsAllKeysEvenWhenNothingWasFound(): void
    {
        $json = json_decode(
            (new JsonResultFormatter())->format(ResultFixture::emptyResult()),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );

        $this->assertSame(
            [
                'directories',
                'files',
                'linesOfCode',
                'length',
                'averageComplexityPerLogicalLine',
                'classesOrTraits',
                'classes',
                'methods',
                'functions',
                'namespaces',
                'interfaces',
                'traits',
                'enums',
                'constants',
                'dependencies',
                'tests',
                'errors',
            ],
            array_keys($json),
        );

        $this->assertSame(0, $json['classes']['count']);
        $this->assertSame(0.0, $json['classes']['cyclomaticComplexity']['average']);
        $this->assertSame(0, $json['methods']['count']);
        $this->assertSame(0.0, $json['methods']['cyclomaticComplexity']['average']);
        $this->assertSame(0, $json['functions']['count']);
        $this->assertSame(0.0, $json['functions']['cyclomaticComplexity']['average']);
        $this->assertSame(0, $json['tests']['classes']);
    }
}
