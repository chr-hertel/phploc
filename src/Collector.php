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

/**
 * Accumulates the measurements of all analysed files.
 *
 * One instance is shared by the MetricsVisitor of every file of a single run;
 * result() then turns what was collected into the immutable Result.
 *
 * @internal
 */
final class Collector
{
    /**
     * @var non-negative-int
     */
    private int $linesOfCode = 0;

    /**
     * @var non-negative-int
     */
    private int $commentLinesOfCode = 0;

    /**
     * @var non-negative-int
     */
    private int $nonCommentLinesOfCode = 0;

    /**
     * @var non-negative-int
     */
    private int $logicalLinesInClasses = 0;

    /**
     * @var non-negative-int
     */
    private int $logicalLinesInFunctions = 0;

    /**
     * @var non-negative-int
     */
    private int $logicalLinesNotInClassesOrFunctions = 0;

    /**
     * @var non-negative-int
     */
    private int $decisionPoints = 0;

    /**
     * @var list<non-negative-int>
     */
    private array $classLengths = [];

    /**
     * @var list<non-negative-int>
     */
    private array $classComplexities = [];

    /**
     * @var list<non-negative-int>
     */
    private array $methodsPerClass = [];

    /**
     * @var list<non-negative-int>
     */
    private array $methodLengths = [];

    /**
     * @var list<non-negative-int>
     */
    private array $methodComplexities = [];

    /**
     * @var list<non-negative-int>
     */
    private array $functionComplexities = [];

    /**
     * @var array<non-empty-string, true>
     */
    private array $namespaces = [];

    /**
     * @var non-negative-int
     */
    private int $interfaces = 0;

    /**
     * @var non-negative-int
     */
    private int $traits = 0;

    /**
     * @var non-negative-int
     */
    private int $enums = 0;

    /**
     * @var non-negative-int
     */
    private int $abstractClasses = 0;

    /**
     * @var non-negative-int
     */
    private int $finalClasses = 0;

    /**
     * @var non-negative-int
     */
    private int $nonFinalClasses = 0;

    /**
     * @var non-negative-int
     */
    private int $nonStaticMethods = 0;

    /**
     * @var non-negative-int
     */
    private int $staticMethods = 0;

    /**
     * @var non-negative-int
     */
    private int $publicMethods = 0;

    /**
     * @var non-negative-int
     */
    private int $protectedMethods = 0;

    /**
     * @var non-negative-int
     */
    private int $privateMethods = 0;

    /**
     * @var non-negative-int
     */
    private int $namedFunctions = 0;

    /**
     * @var non-negative-int
     */
    private int $anonymousFunctions = 0;

    /**
     * @var non-negative-int
     */
    private int $globalConstants = 0;

    /**
     * @var non-negative-int
     */
    private int $publicClassConstants = 0;

    /**
     * @var non-negative-int
     */
    private int $nonPublicClassConstants = 0;

    /**
     * @var array<non-empty-string, true>
     */
    private array $definedConstants = [];

    /**
     * @var list<non-empty-string>
     */
    private array $constantAccesses = [];

    /**
     * @var non-negative-int
     */
    private int $globalVariableAccesses = 0;

    /**
     * @var non-negative-int
     */
    private int $superGlobalVariableAccesses = 0;

    /**
     * @var non-negative-int
     */
    private int $nonStaticAttributeAccesses = 0;

    /**
     * @var non-negative-int
     */
    private int $staticAttributeAccesses = 0;

    /**
     * @var non-negative-int
     */
    private int $nonStaticMethodCalls = 0;

    /**
     * @var non-negative-int
     */
    private int $staticMethodCalls = 0;

    /**
     * @var non-negative-int
     */
    private int $testClasses = 0;

    /**
     * @var non-negative-int
     */
    private int $testMethods = 0;

    /**
     * @param non-negative-int $linesOfCode
     * @param non-negative-int $commentLinesOfCode
     * @param non-negative-int $nonCommentLinesOfCode
     */
    public function addLines(int $linesOfCode, int $commentLinesOfCode, int $nonCommentLinesOfCode): void
    {
        $this->linesOfCode += $linesOfCode;
        $this->commentLinesOfCode += $commentLinesOfCode;
        $this->nonCommentLinesOfCode += $nonCommentLinesOfCode;
    }

    public function addLogicalLineInFunction(): void
    {
        ++$this->logicalLinesInFunctions;
    }

    public function addLogicalLineNotInClassOrFunction(): void
    {
        ++$this->logicalLinesNotInClassesOrFunctions;
    }

    /**
     * @param non-negative-int $decisionPoints
     */
    public function addDecisionPoints(int $decisionPoints): void
    {
        $this->decisionPoints += $decisionPoints;
    }

    /**
     * Interfaces contribute their logical lines but are not part of the class
     * statistics, because they have no implementation to measure.
     *
     * @param non-negative-int $logicalLines
     * @param non-negative-int $complexity
     * @param non-negative-int $methods
     */
    public function addClassLike(int $logicalLines, int $complexity, int $methods, bool $includeInStatistics): void
    {
        $this->logicalLinesInClasses += $logicalLines;

        if (!$includeInStatistics) {
            return;
        }

        $this->classLengths[] = $logicalLines;
        $this->classComplexities[] = $complexity;
        $this->methodsPerClass[] = $methods;
    }

    public function addNamespace(string $namespace): void
    {
        if ('' === $namespace) {
            return;
        }

        $this->namespaces[$namespace] = true;
    }

    public function addInterface(): void
    {
        ++$this->interfaces;
    }

    public function addTrait(): void
    {
        ++$this->traits;
    }

    public function addEnum(): void
    {
        ++$this->enums;
    }

    public function addAbstractClass(): void
    {
        ++$this->abstractClasses;
    }

    public function addFinalClass(): void
    {
        ++$this->finalClasses;
    }

    public function addNonFinalClass(): void
    {
        ++$this->nonFinalClasses;
    }

    public function addMethod(bool $static, Visibility $visibility): void
    {
        if ($static) {
            ++$this->staticMethods;
        } else {
            ++$this->nonStaticMethods;
        }

        match ($visibility) {
            Visibility::Public => ++$this->publicMethods,
            Visibility::Protected => ++$this->protectedMethods,
            Visibility::Private => ++$this->privateMethods,
        };
    }

    /**
     * @param non-negative-int $logicalLines
     * @param non-negative-int $complexity
     */
    public function addMethodBody(int $logicalLines, int $complexity): void
    {
        $this->methodLengths[] = $logicalLines;
        $this->methodComplexities[] = $complexity;
    }

    /**
     * @param non-negative-int $complexity
     */
    public function addNamedFunction(int $complexity): void
    {
        ++$this->namedFunctions;
        $this->functionComplexities[] = $complexity;
    }

    /**
     * @param non-negative-int $complexity
     */
    public function addAnonymousFunction(int $complexity): void
    {
        ++$this->anonymousFunctions;
        $this->functionComplexities[] = $complexity;
    }

    public function addGlobalConstant(?string $name): void
    {
        ++$this->globalConstants;

        if (null === $name || '' === $name) {
            return;
        }

        $this->definedConstants[$name] = true;
    }

    /**
     * @param non-negative-int $count
     */
    public function addClassConstants(int $count, bool $public): void
    {
        if ($public) {
            $this->publicClassConstants += $count;
        } else {
            $this->nonPublicClassConstants += $count;
        }
    }

    public function addConstantAccess(string $name): void
    {
        if ('' === $name) {
            return;
        }

        $this->constantAccesses[] = $name;
    }

    public function addGlobalVariableAccess(): void
    {
        ++$this->globalVariableAccesses;
    }

    public function addSuperGlobalVariableAccess(): void
    {
        ++$this->superGlobalVariableAccesses;
    }

    public function addAttributeAccess(bool $static): void
    {
        if ($static) {
            ++$this->staticAttributeAccesses;
        } else {
            ++$this->nonStaticAttributeAccesses;
        }
    }

    public function addMethodCall(bool $static): void
    {
        if ($static) {
            ++$this->staticMethodCalls;
        } else {
            ++$this->nonStaticMethodCalls;
        }
    }

    public function addTestClass(): void
    {
        ++$this->testClasses;
    }

    public function addTestMethod(): void
    {
        ++$this->testMethods;
    }

    /**
     * @param list<non-empty-string> $errors
     * @param non-negative-int       $directories
     * @param non-negative-int       $files
     */
    public function result(array $errors, int $directories, int $files): Result
    {
        $logicalLinesOfCode = $this->logicalLinesInClasses + $this->logicalLinesInFunctions + $this->logicalLinesNotInClassesOrFunctions;
        $functions = $this->namedFunctions + $this->anonymousFunctions;

        return new Result(
            $errors,
            $directories,
            $files,
            new Size(
                $this->linesOfCode,
                $this->commentLinesOfCode,
                $this->nonCommentLinesOfCode,
                $logicalLinesOfCode,
                $this->logicalLinesInClasses,
                $this->logicalLinesInFunctions,
                $this->logicalLinesNotInClassesOrFunctions,
                Statistics::fromValues($this->classLengths),
                Statistics::fromValues($this->methodLengths),
                Statistics::fromValues($this->methodsPerClass),
                $this->divide($this->logicalLinesInFunctions, $functions),
            ),
            new Complexity(
                $this->divide($this->decisionPoints, $logicalLinesOfCode),
                Statistics::fromValues($this->classComplexities),
                Statistics::fromValues($this->methodComplexities),
                Statistics::fromValues($this->functionComplexities),
            ),
            new Dependencies(
                $this->globalConstantAccesses(),
                $this->globalVariableAccesses,
                $this->superGlobalVariableAccesses,
                $this->nonStaticAttributeAccesses,
                $this->staticAttributeAccesses,
                $this->nonStaticMethodCalls,
                $this->staticMethodCalls,
            ),
            new Structure(
                \count($this->namespaces),
                $this->interfaces,
                $this->traits,
                $this->enums,
                $this->abstractClasses,
                $this->finalClasses,
                $this->nonFinalClasses,
                $this->nonStaticMethods,
                $this->staticMethods,
                $this->publicMethods,
                $this->protectedMethods,
                $this->privateMethods,
                $this->namedFunctions,
                $this->anonymousFunctions,
                $this->globalConstants,
                $this->publicClassConstants,
                $this->nonPublicClassConstants,
            ),
            new Tests($this->testClasses, $this->testMethods),
        );
    }

    /**
     * Only constants that are defined by the analysed code itself are counted,
     * so that constants of PHP itself or of dependencies are not reported as
     * dependencies of the analysed code.
     *
     * @return non-negative-int
     */
    private function globalConstantAccesses(): int
    {
        $accesses = 0;

        foreach ($this->constantAccesses as $name) {
            if (isset($this->definedConstants[$name])) {
                ++$accesses;
            }
        }

        return $accesses;
    }

    /**
     * @param non-negative-int $dividend
     * @param non-negative-int $divisor
     */
    private function divide(int $dividend, int $divisor): float
    {
        if (0 === $divisor) {
            return 0.0;
        }

        return $dividend / $divisor;
    }
}
