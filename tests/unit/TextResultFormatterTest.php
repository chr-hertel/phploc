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

#[CoversClass(TextResultFormatter::class)]
#[UsesClass(Complexity::class)]
#[UsesClass(Dependencies::class)]
#[UsesClass(Result::class)]
#[UsesClass(Size::class)]
#[UsesClass(Statistics::class)]
#[UsesClass(Structure::class)]
#[UsesClass(Tests::class)]
#[Small]
final class TextResultFormatterTest extends TestCase
{
    public function testFormatsResultAsText(): void
    {
        $this->assertStringEqualsFile(
            __DIR__.'/../_expectations/result.txt',
            (new TextResultFormatter())->format(ResultFixture::result()),
        );
    }

    public function testFormatsErrors(): void
    {
        $this->assertStringEqualsFile(
            __DIR__.'/../_expectations/result-with-errors.txt',
            (new TextResultFormatter())->format(ResultFixture::resultWithErrors()),
        );
    }

    public function testFormatsEveryMetricEvenWhenNothingWasFound(): void
    {
        $this->assertStringEqualsFile(
            __DIR__.'/../_expectations/result-empty.txt',
            (new TextResultFormatter())->format(ResultFixture::emptyResult()),
        );
    }
}
