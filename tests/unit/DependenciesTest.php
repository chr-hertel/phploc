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

#[CoversClass(Dependencies::class)]
#[Small]
final class DependenciesTest extends TestCase
{
    public function testSumsUpGlobalAccesses(): void
    {
        $dependencies = $this->createDependencies();

        $this->assertSame(10, $dependencies->globalAccesses());
        $this->assertSame(5, $dependencies->globalConstantAccesses());
        $this->assertSame(3, $dependencies->globalVariableAccesses());
        $this->assertSame(2, $dependencies->superGlobalVariableAccesses());

        $this->assertSame(50.0, $dependencies->globalConstantAccessesPercentage());
        $this->assertSame(30.0, $dependencies->globalVariableAccessesPercentage());
        $this->assertSame(20.0, $dependencies->superGlobalVariableAccessesPercentage());
    }

    public function testSumsUpAttributeAccesses(): void
    {
        $dependencies = $this->createDependencies();

        $this->assertSame(20, $dependencies->attributeAccesses());
        $this->assertSame(15, $dependencies->nonStaticAttributeAccesses());
        $this->assertSame(5, $dependencies->staticAttributeAccesses());

        $this->assertSame(75.0, $dependencies->nonStaticAttributeAccessesPercentage());
        $this->assertSame(25.0, $dependencies->staticAttributeAccessesPercentage());
    }

    public function testSumsUpMethodCalls(): void
    {
        $dependencies = $this->createDependencies();

        $this->assertSame(40, $dependencies->methodCalls());
        $this->assertSame(30, $dependencies->nonStaticMethodCalls());
        $this->assertSame(10, $dependencies->staticMethodCalls());

        $this->assertSame(75.0, $dependencies->nonStaticMethodCallsPercentage());
        $this->assertSame(25.0, $dependencies->staticMethodCallsPercentage());
    }

    public function testHasNoPercentagesWhenThereAreNoDependencies(): void
    {
        $dependencies = new Dependencies(0, 0, 0, 0, 0, 0, 0);

        $this->assertSame(0.0, $dependencies->globalConstantAccessesPercentage());
        $this->assertSame(0.0, $dependencies->globalVariableAccessesPercentage());
        $this->assertSame(0.0, $dependencies->superGlobalVariableAccessesPercentage());
        $this->assertSame(0.0, $dependencies->nonStaticAttributeAccessesPercentage());
        $this->assertSame(0.0, $dependencies->staticAttributeAccessesPercentage());
        $this->assertSame(0.0, $dependencies->nonStaticMethodCallsPercentage());
        $this->assertSame(0.0, $dependencies->staticMethodCallsPercentage());
    }

    private function createDependencies(): Dependencies
    {
        return new Dependencies(5, 3, 2, 15, 5, 30, 10);
    }
}
