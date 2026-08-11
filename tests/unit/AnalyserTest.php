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

#[CoversClass(Analyser::class)]
#[CoversClass(Collector::class)]
#[CoversClass(MetricsVisitor::class)]
#[CoversClass(Scope::class)]
#[CoversClass(TestClassRegistry::class)]
#[CoversClass(TestDetectionVisitor::class)]
#[CoversClass(Visibility::class)]
#[UsesClass(Complexity::class)]
#[UsesClass(Dependencies::class)]
#[UsesClass(Result::class)]
#[UsesClass(Size::class)]
#[UsesClass(Statistics::class)]
#[UsesClass(Structure::class)]
#[UsesClass(Tests::class)]
#[Small]
final class AnalyserTest extends TestCase
{
    public function testAnalysesFiles(): void
    {
        $result = $this->analyse(
            '_fixture/example_function.php',
            '_fixture/ExampleClass.php',
            '_fixture/ExampleInterface.php',
            '_fixture/ExampleTrait.php',
        );

        $this->assertFalse($result->hasErrors());
        $this->assertSame(1, $result->directories());
        $this->assertSame(4, $result->files());
        $this->assertSame(152, $result->size()->linesOfCode());
        $this->assertSame(32, $result->size()->commentLinesOfCode());
        $this->assertSame(120, $result->size()->nonCommentLinesOfCode());
        $this->assertSame(40, $result->size()->logicalLinesOfCode());
        $this->assertSame(1, $result->structure()->functions());
        $this->assertSame(2, $result->structure()->classesOrTraits());
        $this->assertSame(4, $result->structure()->methods());
    }

    public function testSplitsLogicalLinesOfCodeIntoClassesFunctionsAndTheRest(): void
    {
        $size = $this->analyse(
            '_fixture/example_function.php',
            '_fixture/ExampleClass.php',
            '_fixture/ExampleInterface.php',
            '_fixture/ExampleTrait.php',
        )->size();

        $this->assertSame(24, $size->logicalLinesInClasses());
        $this->assertSame(12, $size->logicalLinesInFunctions());
        $this->assertSame(4, $size->logicalLinesNotInClassesOrFunctions());

        $this->assertSame(
            $size->logicalLinesOfCode(),
            $size->logicalLinesInClasses() + $size->logicalLinesInFunctions() + $size->logicalLinesNotInClassesOrFunctions(),
        );
    }

    public function testMeasuresTheLengthOfClassesAndMethods(): void
    {
        $size = $this->analyse('_fixture-metrics/MethodsPerClass.php')->size();

        $this->assertSame(0, $size->classLength()->minimum());
        $this->assertSame(1.0, $size->classLength()->average());
        $this->assertSame(3, $size->classLength()->maximum());

        $this->assertSame(0, $size->methodLength()->minimum());
        $this->assertSame(0.5, $size->methodLength()->average());
        $this->assertSame(2, $size->methodLength()->maximum());
    }

    public function testProvidesAverageMinimumAndMaximumNumberOfMethodsPerClass(): void
    {
        $size = $this->analyse('_fixture-metrics/MethodsPerClass.php')->size();

        $this->assertSame(0, $size->methodsPerClass()->minimum());
        $this->assertSame(2.0, $size->methodsPerClass()->average());
        $this->assertSame(4, $size->methodsPerClass()->maximum());
    }

    public function testMeasuresTheComplexityOfClassesMethodsAndFunctions(): void
    {
        $result = $this->analyse('_fixture/ExampleClass.php', '_fixture/example_function.php');
        $complexity = $result->complexity();

        $this->assertSame(14, $complexity->classes()->minimum());
        $this->assertSame(14.0, $complexity->classes()->average());
        $this->assertSame(14, $complexity->classes()->maximum());

        $this->assertSame(14, $complexity->methods()->minimum());
        $this->assertSame(14, $complexity->methods()->maximum());

        $this->assertSame(14, $complexity->functions()->minimum());
        $this->assertSame(14, $complexity->functions()->maximum());

        // 13 decision points and 13 logical lines per file
        $this->assertSame(26, $result->size()->logicalLinesOfCode());
        $this->assertSame(1.0, $complexity->averagePerLogicalLine());
    }

    public function testDoesNotMeasureTheComplexityOfMethodsWithoutABody(): void
    {
        $result = $this->analyse('_fixture/ExampleInterface.php');

        $this->assertSame(1, $result->structure()->methods());
        $this->assertSame(0, $result->complexity()->methods()->maximum());
    }

    public function testCountsDependencies(): void
    {
        $dependencies = $this->analyse('_fixture-metrics/Dependencies.php')->dependencies();

        $this->assertSame(5, $dependencies->globalAccesses());
        $this->assertSame(2, $dependencies->globalConstantAccesses());
        $this->assertSame(2, $dependencies->globalVariableAccesses());
        $this->assertSame(1, $dependencies->superGlobalVariableAccesses());

        $this->assertSame(4, $dependencies->attributeAccesses());
        $this->assertSame(3, $dependencies->nonStaticAttributeAccesses());
        $this->assertSame(1, $dependencies->staticAttributeAccesses());

        $this->assertSame(3, $dependencies->methodCalls());
        $this->assertSame(2, $dependencies->nonStaticMethodCalls());
        $this->assertSame(1, $dependencies->staticMethodCalls());
    }

    public function testCountsDependenciesThatCannotBeResolvedStatically(): void
    {
        $result = $this->analyse('_fixture-metrics/EdgeCases.php');

        // define() with a name that is only known at runtime still counts as a
        // global constant, but nothing can be counted as an access to it
        $this->assertSame(1, $result->structure()->globalConstants());
        $this->assertSame(0, $result->dependencies()->globalConstantAccesses());
        $this->assertSame(0, $result->dependencies()->globalVariableAccesses());

        $this->assertSame(2, $result->dependencies()->nonStaticAttributeAccesses());
        $this->assertSame(0, $result->dependencies()->methodCalls());
    }

    public function testCollectsTheNumberOfFinalNonFinalAndAbstractClasses(): void
    {
        $structure = $this->analyse('_fixture-metrics/Classes.php')->structure();

        $this->assertSame(4, $structure->classes());
        $this->assertSame(1, $structure->abstractClasses());
        $this->assertSame(3, $structure->concreteClasses());
        $this->assertSame(1, $structure->finalClasses());
        $this->assertSame(2, $structure->nonFinalClasses());
    }

    public function testCountsInterfacesTraitsEnumsAndNamespaces(): void
    {
        $structure = $this->analyse('_fixture-metrics/Classes.php')->structure();

        $this->assertSame(1, $structure->interfaces());
        $this->assertSame(1, $structure->traits());
        $this->assertSame(1, $structure->enums());
        $this->assertSame(1, $structure->namespaces());
    }

    public function testMakesADistinctionBetweenTheScopeAndTheVisibilityOfMethods(): void
    {
        $structure = $this->analyse('_fixture-metrics/Methods.php')->structure();

        $this->assertSame(8, $structure->methods());
        $this->assertSame(6, $structure->nonStaticMethods());
        $this->assertSame(2, $structure->staticMethods());
        $this->assertSame(5, $structure->publicMethods());
        $this->assertSame(1, $structure->protectedMethods());
        $this->assertSame(2, $structure->privateMethods());
    }

    public function testMakesADistinctionBetweenNamedAndAnonymousFunctions(): void
    {
        $structure = $this->analyse('_fixture-metrics/Functions.php')->structure();

        $this->assertSame(3, $structure->functions());
        $this->assertSame(1, $structure->namedFunctions());
        $this->assertSame(2, $structure->anonymousFunctions());
    }

    public function testMakesADistinctionBetweenPublicAndNonPublicClassConstants(): void
    {
        $structure = $this->analyse('_fixture-metrics/Constants.php')->structure();

        $this->assertSame(7, $structure->constants());
        $this->assertSame(2, $structure->globalConstants());
        $this->assertSame(5, $structure->classConstants());
        $this->assertSame(3, $structure->publicClassConstants());
        $this->assertSame(2, $structure->nonPublicClassConstants());
    }

    public function testDoesNotCountUsingATraitAsALogicalLine(): void
    {
        $result = $this->analyse('_fixture-metrics/TraitUsage.php');

        $this->assertSame(2, $result->size()->logicalLinesOfCode());
        $this->assertSame(2, $result->size()->logicalLinesInClasses());
        $this->assertSame(1, $result->structure()->traits());
        $this->assertSame(1, $result->structure()->classes());
    }

    public function testCountsEnumsSeparatelyFromClasses(): void
    {
        $result = $this->analyse('_fixture-metrics/Enums.php');

        $this->assertSame(1, $result->structure()->enums());
        $this->assertSame(0, $result->structure()->classes());
        $this->assertSame(1, $result->structure()->classesOrTraits());
        $this->assertSame(1, $result->structure()->publicClassConstants());
        $this->assertSame(3, $result->complexity()->classes()->maximum());
    }

    public function testCountsAnonymousClassesAsConcreteNonFinalClasses(): void
    {
        $structure = $this->analyse('_fixture-metrics/Classes.php')->structure();

        $this->assertSame(2, $structure->nonFinalClasses());
        $this->assertSame(1, $structure->methods());
    }

    public function testCountsTestClassesAndTestMethodsInsteadOfMeasuringThem(): void
    {
        $result = $this->analyse(
            '_fixture-tests/ExampleTest.php',
            '_fixture-tests/LegacyTest.php',
            '_fixture-tests/IntermediateBase.php',
            '_fixture-tests/IndirectTest.php',
            '_fixture-tests/PestTest.php',
            '_fixture-tests/StandaloneTest.php',
            '_fixture-tests/ProductionClass.php',
        );

        $this->assertSame(6, $result->tests()->classes());
        $this->assertSame(7, $result->tests()->methods());

        // Only ProductionClass is left to measure
        $this->assertSame(1, $result->structure()->classes());
        $this->assertSame(1, $result->structure()->methods());
        $this->assertSame(1, $result->size()->logicalLinesOfCode());
    }

    public function testRecognisesClassesThatExtendPhpUnitTestCase(): void
    {
        $result = $this->analyse('_fixture-tests/ExampleTest.php');

        $this->assertSame(1, $result->tests()->classes());
        $this->assertSame(2, $result->tests()->methods());
    }

    public function testRecognisesClassesThatExtendTheOldPhpUnitTestCase(): void
    {
        $result = $this->analyse('_fixture-tests/LegacyTest.php');

        $this->assertSame(1, $result->tests()->classes());
        $this->assertSame(1, $result->tests()->methods());
    }

    public function testRecognisesClassesThatIndirectlyExtendPhpUnitTestCase(): void
    {
        $result = $this->analyse(
            '_fixture-tests/IntermediateBase.php',
            '_fixture-tests/IndirectTest.php',
        );

        $this->assertSame(2, $result->tests()->classes());
        $this->assertSame(1, $result->tests()->methods());
    }

    public function testFallsBackToRecognisingClassesThatAreNamedLikeATestAndDeclareTestMethods(): void
    {
        // The base class of IndirectTest is not analysed here, so its ancestry
        // does not reach a known test case class
        $result = $this->analyse('_fixture-tests/IndirectTest.php');

        $this->assertSame(1, $result->tests()->classes());
        $this->assertSame(1, $result->tests()->methods());
    }

    public function testRecognisesATestClassThatExtendsNothing(): void
    {
        $result = $this->analyse('_fixture-tests/StandaloneTest.php');

        $this->assertSame(1, $result->tests()->classes());
        $this->assertSame(1, $result->tests()->methods());
        $this->assertSame(0, $result->structure()->classes());
    }

    public function testRecognisesPestFiles(): void
    {
        $result = $this->analyse('_fixture-tests/PestTest.php');

        $this->assertSame(1, $result->tests()->classes());
        $this->assertSame(2, $result->tests()->methods());
        $this->assertSame(0, $result->structure()->functions());
        $this->assertSame(0, $result->size()->logicalLinesOfCode());
    }

    public function testDoesNotCountProductionCodeAsTests(): void
    {
        $result = $this->analyse('_fixture-tests/ProductionClass.php');

        $this->assertSame(0, $result->tests()->classes());
        $this->assertSame(0, $result->tests()->methods());
        $this->assertSame(1, $result->structure()->classes());
    }

    public function testCountsDirectoriesThatContainAnalysedFiles(): void
    {
        $result = $this->analyse(
            '_fixture/ExampleInterface.php',
            '_fixture-single-line/single_line.php',
        );

        $this->assertSame(2, $result->directories());
        $this->assertSame(2, $result->files());
    }

    public function testCollectsErrorForFileThatCannotBeParsed(): void
    {
        $file = __DIR__.'/../_fixture-invalid/InvalidClass.php';

        $result = (new Analyser())->analyse([$file]);

        $this->assertTrue($result->hasErrors());
        $this->assertCount(1, $result->errors());
        $this->assertStringStartsWith('Cannot parse '.$file.':', $result->errors()[0]);
    }

    public function testCountsFileThatCannotBeParsedButDoesNotMeasureIt(): void
    {
        $result = $this->analyse(
            '_fixture-invalid/InvalidClass.php',
            '_fixture-single-line/single_line.php',
        );

        $this->assertSame(2, $result->files());
        $this->assertSame(1, $result->size()->linesOfCode());
    }

    public function testIgnoresFileThatCannotBeParsedWhenLookingForTests(): void
    {
        $result = $this->analyse(
            '_fixture-invalid/InvalidClass.php',
            '_fixture-tests/ExampleTest.php',
        );

        $this->assertTrue($result->hasErrors());
        $this->assertSame(1, $result->tests()->classes());
    }

    public function testAnalysesFileWithoutFunctionsOrMethods(): void
    {
        $result = $this->analyse('_fixture/ExampleInterface.php');

        $this->assertSame(0, $result->structure()->functions());
        $this->assertSame(0, $result->structure()->classesOrTraits());
        $this->assertSame(0, $result->complexity()->functions()->minimum());
        $this->assertSame(0.0, $result->complexity()->functions()->average());
        $this->assertSame(0, $result->complexity()->functions()->maximum());
        $this->assertSame(0, $result->complexity()->methods()->minimum());
        $this->assertSame(0.0, $result->complexity()->methods()->average());
        $this->assertSame(0, $result->complexity()->methods()->maximum());
    }

    public function testAnalysesFileThatDoesNotEndWithNewline(): void
    {
        $size = $this->analyse('_fixture-single-line/single_line.php')->size();

        $this->assertSame(1, $size->linesOfCode());
        $this->assertSame(0, $size->commentLinesOfCode());
        $this->assertSame(1, $size->nonCommentLinesOfCode());
    }

    public function testAnalysesEmptyFile(): void
    {
        $result = $this->analyse('_fixture-empty/empty.php');

        $this->assertFalse($result->hasErrors());
        $this->assertSame(1, $result->files());
        $this->assertSame(0, $result->size()->linesOfCode());
        $this->assertSame(0, $result->size()->commentLinesOfCode());
        $this->assertSame(0, $result->size()->nonCommentLinesOfCode());
        $this->assertSame(0, $result->size()->logicalLinesOfCode());
    }

    public function testIgnoresEmptyFileWhenLookingForTests(): void
    {
        $result = $this->analyse('_fixture-empty/empty.php');

        $this->assertSame(0, $result->tests()->classes());
    }

    private function analyse(string ...$fixtures): Result
    {
        return (new Analyser())->analyse($this->files($fixtures));
    }

    /**
     * @param list<non-empty-string> $fixtures
     *
     * @return list<non-empty-string>
     */
    private function files(array $fixtures): array
    {
        $files = [];

        foreach ($fixtures as $fixture) {
            $files[] = __DIR__.'/../'.$fixture;
        }

        return $files;
    }
}
