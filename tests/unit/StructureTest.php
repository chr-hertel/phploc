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

#[CoversClass(Structure::class)]
#[Small]
final class StructureTest extends TestCase
{
    public function testHasNamespacesInterfacesTraitsAndEnums(): void
    {
        $structure = $this->createStructure();

        $this->assertSame(3, $structure->namespaces());
        $this->assertSame(1, $structure->interfaces());
        $this->assertSame(2, $structure->traits());
        $this->assertSame(4, $structure->enums());
    }

    public function testSumsUpClasses(): void
    {
        $structure = $this->createStructure();

        $this->assertSame(10, $structure->classes());
        $this->assertSame(2, $structure->abstractClasses());
        $this->assertSame(8, $structure->concreteClasses());
        $this->assertSame(6, $structure->finalClasses());
        $this->assertSame(2, $structure->nonFinalClasses());

        $this->assertSame(20.0, $structure->abstractClassesPercentage());
        $this->assertSame(80.0, $structure->concreteClassesPercentage());
        $this->assertSame(75.0, $structure->finalClassesPercentage());
        $this->assertSame(25.0, $structure->nonFinalClassesPercentage());
    }

    public function testCountsClassesTraitsAndEnumsAsThingsThatDeclareMethods(): void
    {
        $this->assertSame(16, $this->createStructure()->classesOrTraits());
    }

    public function testSumsUpMethods(): void
    {
        $structure = $this->createStructure();

        $this->assertSame(20, $structure->methods());
        $this->assertSame(16, $structure->nonStaticMethods());
        $this->assertSame(4, $structure->staticMethods());
        $this->assertSame(10, $structure->publicMethods());
        $this->assertSame(6, $structure->protectedMethods());
        $this->assertSame(4, $structure->privateMethods());

        $this->assertSame(80.0, $structure->nonStaticMethodsPercentage());
        $this->assertSame(20.0, $structure->staticMethodsPercentage());
        $this->assertSame(50.0, $structure->publicMethodsPercentage());
        $this->assertSame(30.0, $structure->protectedMethodsPercentage());
        $this->assertSame(20.0, $structure->privateMethodsPercentage());
    }

    public function testSumsUpFunctions(): void
    {
        $structure = $this->createStructure();

        $this->assertSame(5, $structure->functions());
        $this->assertSame(4, $structure->namedFunctions());
        $this->assertSame(1, $structure->anonymousFunctions());

        $this->assertSame(80.0, $structure->namedFunctionsPercentage());
        $this->assertSame(20.0, $structure->anonymousFunctionsPercentage());
    }

    public function testSumsUpConstants(): void
    {
        $structure = $this->createStructure();

        $this->assertSame(10, $structure->constants());
        $this->assertSame(2, $structure->globalConstants());
        $this->assertSame(8, $structure->classConstants());
        $this->assertSame(6, $structure->publicClassConstants());
        $this->assertSame(2, $structure->nonPublicClassConstants());

        $this->assertSame(20.0, $structure->globalConstantsPercentage());
        $this->assertSame(80.0, $structure->classConstantsPercentage());
        $this->assertSame(75.0, $structure->publicClassConstantsPercentage());
        $this->assertSame(25.0, $structure->nonPublicClassConstantsPercentage());
    }

    public function testHasNoPercentagesWhenThereIsNoStructure(): void
    {
        $structure = new Structure(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

        $this->assertSame(0.0, $structure->abstractClassesPercentage());
        $this->assertSame(0.0, $structure->concreteClassesPercentage());
        $this->assertSame(0.0, $structure->finalClassesPercentage());
        $this->assertSame(0.0, $structure->nonFinalClassesPercentage());
        $this->assertSame(0.0, $structure->nonStaticMethodsPercentage());
        $this->assertSame(0.0, $structure->staticMethodsPercentage());
        $this->assertSame(0.0, $structure->publicMethodsPercentage());
        $this->assertSame(0.0, $structure->protectedMethodsPercentage());
        $this->assertSame(0.0, $structure->privateMethodsPercentage());
        $this->assertSame(0.0, $structure->namedFunctionsPercentage());
        $this->assertSame(0.0, $structure->anonymousFunctionsPercentage());
        $this->assertSame(0.0, $structure->globalConstantsPercentage());
        $this->assertSame(0.0, $structure->classConstantsPercentage());
        $this->assertSame(0.0, $structure->publicClassConstantsPercentage());
        $this->assertSame(0.0, $structure->nonPublicClassConstantsPercentage());
    }

    private function createStructure(): Structure
    {
        return new Structure(3, 1, 2, 4, 2, 6, 2, 16, 4, 10, 6, 4, 4, 1, 2, 6, 2);
    }
}
