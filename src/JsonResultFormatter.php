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

final readonly class JsonResultFormatter
{
    /**
     * @return non-empty-string
     *
     * @throws \JsonException
     */
    public function format(Result $result): string
    {
        $size = $result->size();
        $complexity = $result->complexity();
        $dependencies = $result->dependencies();
        $structure = $result->structure();
        $tests = $result->tests();

        $json = json_encode(
            [
                'directories' => $result->directories(),
                'files' => $result->files(),
                'linesOfCode' => [
                    'total' => $size->linesOfCode(),
                    'comment' => $size->commentLinesOfCode(),
                    'commentPercentage' => $size->commentLinesOfCodePercentage(),
                    'nonComment' => $size->nonCommentLinesOfCode(),
                    'nonCommentPercentage' => $size->nonCommentLinesOfCodePercentage(),
                    'logical' => $size->logicalLinesOfCode(),
                    'logicalPercentage' => $size->logicalLinesOfCodePercentage(),
                    'logicalInClasses' => $size->logicalLinesInClasses(),
                    'logicalInClassesPercentage' => $size->logicalLinesInClassesPercentage(),
                    'logicalInFunctions' => $size->logicalLinesInFunctions(),
                    'logicalInFunctionsPercentage' => $size->logicalLinesInFunctionsPercentage(),
                    'logicalNotInClassesOrFunctions' => $size->logicalLinesNotInClassesOrFunctions(),
                    'logicalNotInClassesOrFunctionsPercentage' => $size->logicalLinesNotInClassesOrFunctionsPercentage(),
                ],
                'length' => [
                    'class' => $this->statistics($size->classLength()),
                    'method' => $this->statistics($size->methodLength()),
                    'methodsPerClass' => $this->statistics($size->methodsPerClass()),
                    'averageFunction' => $size->averageFunctionLength(),
                ],
                'averageComplexityPerLogicalLine' => $complexity->averagePerLogicalLine(),
                'classesOrTraits' => $structure->classesOrTraits(),
                'classes' => [
                    'count' => $structure->classes(),
                    'abstract' => $structure->abstractClasses(),
                    'concrete' => $structure->concreteClasses(),
                    'final' => $structure->finalClasses(),
                    'nonFinal' => $structure->nonFinalClasses(),
                    'cyclomaticComplexity' => $this->cyclomaticComplexity($complexity->classes()),
                ],
                'methods' => [
                    'count' => $structure->methods(),
                    'cyclomaticComplexity' => $this->cyclomaticComplexity($complexity->methods()),
                    'nonStatic' => $structure->nonStaticMethods(),
                    'static' => $structure->staticMethods(),
                    'public' => $structure->publicMethods(),
                    'protected' => $structure->protectedMethods(),
                    'private' => $structure->privateMethods(),
                ],
                'functions' => [
                    'count' => $structure->functions(),
                    'cyclomaticComplexity' => $this->cyclomaticComplexity($complexity->functions()),
                    'named' => $structure->namedFunctions(),
                    'anonymous' => $structure->anonymousFunctions(),
                ],
                'namespaces' => $structure->namespaces(),
                'interfaces' => $structure->interfaces(),
                'traits' => $structure->traits(),
                'enums' => $structure->enums(),
                'constants' => [
                    'count' => $structure->constants(),
                    'global' => $structure->globalConstants(),
                    'class' => $structure->classConstants(),
                    'publicClass' => $structure->publicClassConstants(),
                    'nonPublicClass' => $structure->nonPublicClassConstants(),
                ],
                'dependencies' => [
                    'globalAccesses' => [
                        'count' => $dependencies->globalAccesses(),
                        'constants' => $dependencies->globalConstantAccesses(),
                        'variables' => $dependencies->globalVariableAccesses(),
                        'superGlobalVariables' => $dependencies->superGlobalVariableAccesses(),
                    ],
                    'attributeAccesses' => [
                        'count' => $dependencies->attributeAccesses(),
                        'nonStatic' => $dependencies->nonStaticAttributeAccesses(),
                        'static' => $dependencies->staticAttributeAccesses(),
                    ],
                    'methodCalls' => [
                        'count' => $dependencies->methodCalls(),
                        'nonStatic' => $dependencies->nonStaticMethodCalls(),
                        'static' => $dependencies->staticMethodCalls(),
                    ],
                ],
                'tests' => [
                    'classes' => $tests->classes(),
                    'methods' => $tests->methods(),
                ],
                'errors' => $result->errors(),
            ],
            \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_INVALID_UTF8_SUBSTITUTE | \JSON_PRESERVE_ZERO_FRACTION | \JSON_THROW_ON_ERROR,
        );

        return $json.\PHP_EOL;
    }

    /**
     * @return array{minimum: non-negative-int, average: float, maximum: non-negative-int}
     */
    private function statistics(Statistics $statistics): array
    {
        return [
            'minimum' => $statistics->minimum(),
            'average' => $statistics->average(),
            'maximum' => $statistics->maximum(),
        ];
    }

    /**
     * @return array{lowest: non-negative-int, average: float, highest: non-negative-int}
     */
    private function cyclomaticComplexity(Statistics $statistics): array
    {
        return [
            'lowest' => $statistics->minimum(),
            'average' => $statistics->average(),
            'highest' => $statistics->maximum(),
        ];
    }
}
