<?php

/*
 * This file is part of PHPLOC.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hertel\PhpLoc\Measurement;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

/**
 * TestClassRegistry::couldDeclareTests() decides whether a file is parsed for
 * the test detection pass at all, so a file it rules out wrongly would have
 * its test classes measured as production code. These tests pin down what it
 * must let through.
 */
#[CoversClass(TestClassRegistry::class)]
#[Small]
final class TestClassRegistryTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function sourceThatCouldDeclareTestsProvider(): iterable
    {
        yield 'class that extends something' => ['<?php class Foo extends Bar {}'];
        yield 'class that is named like a test' => ['<?php final class FooTest { public function testBar() {} }'];
        yield 'imported test case class' => ['<?php use PHPUnit\Framework\TestCase;'];
        yield 'pest test()' => ["<?php test('it works', fn () => null);"];
        yield 'pest it()' => ["<?php it('works', fn () => null);"];
        yield 'pest describe()' => ["<?php describe('a group', fn () => null);"];
        yield 'pest uses()' => ['<?php uses(SomeTrait::class);'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function sourceThatCannotDeclareTestsProvider(): iterable
    {
        yield 'plain class' => ['<?php final class Foo { public function bar() {} }'];
        yield 'function' => ['<?php function foo(): int { return 1; }'];
        yield 'interface' => ['<?php interface Foo { public function bar(); }'];
        yield 'call whose name merely ends in it' => ['<?php $form->submit($data); $x = limit(1);'];
        yield 'empty file' => ['<?php'];
    }

    #[DataProvider('sourceThatCouldDeclareTestsProvider')]
    public function testRecognisesSourceThatCouldDeclareTests(string $source): void
    {
        $this->assertTrue(TestClassRegistry::couldDeclareTests($source));
    }

    #[DataProvider('sourceThatCannotDeclareTestsProvider')]
    public function testRulesOutSourceThatCannotDeclareTests(string $source): void
    {
        $this->assertFalse(TestClassRegistry::couldDeclareTests($source));
    }
}
