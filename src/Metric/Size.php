<?php

/*
 * This file is part of PHPLOC.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hertel\PhpLoc\Metric;

/**
 * The "Size" section of the report.
 */
final readonly class Size
{
    /**
     * @param non-negative-int $linesOfCode
     * @param non-negative-int $commentLinesOfCode
     * @param non-negative-int $nonCommentLinesOfCode
     * @param non-negative-int $logicalLinesOfCode
     * @param non-negative-int $logicalLinesInClasses
     * @param non-negative-int $logicalLinesInFunctions
     * @param non-negative-int $logicalLinesNotInClassesOrFunctions
     */
    public function __construct(
        private int $linesOfCode,
        private int $commentLinesOfCode,
        private int $nonCommentLinesOfCode,
        private int $logicalLinesOfCode,
        private int $logicalLinesInClasses,
        private int $logicalLinesInFunctions,
        private int $logicalLinesNotInClassesOrFunctions,
        private Statistics $classLength,
        private Statistics $methodLength,
        private Statistics $methodsPerClass,
        private float $averageFunctionLength,
    ) {
    }

    /**
     * @return non-negative-int
     */
    public function linesOfCode(): int
    {
        return $this->linesOfCode;
    }

    /**
     * @return non-negative-int
     */
    public function commentLinesOfCode(): int
    {
        return $this->commentLinesOfCode;
    }

    public function commentLinesOfCodePercentage(): float
    {
        return $this->percentageOfLinesOfCode($this->commentLinesOfCode);
    }

    /**
     * @return non-negative-int
     */
    public function nonCommentLinesOfCode(): int
    {
        return $this->nonCommentLinesOfCode;
    }

    public function nonCommentLinesOfCodePercentage(): float
    {
        return $this->percentageOfLinesOfCode($this->nonCommentLinesOfCode);
    }

    /**
     * @return non-negative-int
     */
    public function logicalLinesOfCode(): int
    {
        return $this->logicalLinesOfCode;
    }

    public function logicalLinesOfCodePercentage(): float
    {
        return $this->percentageOfLinesOfCode($this->logicalLinesOfCode);
    }

    /**
     * @return non-negative-int
     */
    public function logicalLinesInClasses(): int
    {
        return $this->logicalLinesInClasses;
    }

    public function logicalLinesInClassesPercentage(): float
    {
        return $this->percentageOfLogicalLinesOfCode($this->logicalLinesInClasses);
    }

    /**
     * @return non-negative-int
     */
    public function logicalLinesInFunctions(): int
    {
        return $this->logicalLinesInFunctions;
    }

    public function logicalLinesInFunctionsPercentage(): float
    {
        return $this->percentageOfLogicalLinesOfCode($this->logicalLinesInFunctions);
    }

    /**
     * @return non-negative-int
     */
    public function logicalLinesNotInClassesOrFunctions(): int
    {
        return $this->logicalLinesNotInClassesOrFunctions;
    }

    public function logicalLinesNotInClassesOrFunctionsPercentage(): float
    {
        return $this->percentageOfLogicalLinesOfCode($this->logicalLinesNotInClassesOrFunctions);
    }

    public function classLength(): Statistics
    {
        return $this->classLength;
    }

    public function methodLength(): Statistics
    {
        return $this->methodLength;
    }

    public function methodsPerClass(): Statistics
    {
        return $this->methodsPerClass;
    }

    public function averageFunctionLength(): float
    {
        return $this->averageFunctionLength;
    }

    /**
     * @param non-negative-int $value
     */
    private function percentageOfLinesOfCode(int $value): float
    {
        if (0 === $this->linesOfCode) {
            return 0.0;
        }

        return ($value / $this->linesOfCode) * 100;
    }

    /**
     * @param non-negative-int $value
     */
    private function percentageOfLogicalLinesOfCode(int $value): float
    {
        if (0 === $this->logicalLinesOfCode) {
            return 0.0;
        }

        return ($value / $this->logicalLinesOfCode) * 100;
    }
}
