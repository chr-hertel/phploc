<?php declare(strict_types=1);
/*
 * This file is part of PHPLOC.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Hertel\PhpLoc;

final readonly class Result
{
    /**
     * @var list<non-empty-string>
     */
    private array $errors;

    /**
     * @var non-negative-int
     */
    private int $directories;

    /**
     * @var non-negative-int
     */
    private int $files;

    /**
     * @var non-negative-int
     */
    private int $linesOfCode;

    /**
     * @var non-negative-int
     */
    private int $commentLinesOfCode;

    /**
     * @var non-negative-int
     */
    private int $nonCommentLinesOfCode;

    /**
     * @var non-negative-int
     */
    private int $logicalLinesOfCode;

    /**
     * @var non-negative-int
     */
    private int $functions;

    /**
     * @var non-negative-int
     */
    private int $lowestCyclomaticComplexityForFunction;
    private float $averageCyclomaticComplexityForFunction;

    /**
     * @var non-negative-int
     */
    private int $highestCyclomaticComplexityForFunction;

    /**
     * @var non-negative-int
     */
    private int $classesOrTraits;

    /**
     * @var non-negative-int
     */
    private int $methods;

    /**
     * @var non-negative-int
     */
    private int $lowestCyclomaticComplexityForMethod;
    private float $averageCyclomaticComplexityForMethod;

    /**
     * @var non-negative-int
     */
    private int $highestCyclomaticComplexityForMethod;

    /**
     * @param list<non-empty-string> $errors
     * @param non-negative-int       $directories
     * @param non-negative-int       $files
     * @param non-negative-int       $linesOfCode
     * @param non-negative-int       $commentLinesOfCode
     * @param non-negative-int       $nonCommentLinesOfCode
     * @param non-negative-int       $logicalLinesOfCode
     * @param non-negative-int       $functions
     * @param non-negative-int       $lowestCyclomaticComplexityForFunction
     * @param non-negative-int       $highestCyclomaticComplexityForFunction
     * @param non-negative-int       $classesOrTraits
     * @param non-negative-int       $methods
     * @param non-negative-int       $lowestCyclomaticComplexityForMethod
     * @param non-negative-int       $highestCyclomaticComplexityForMethod
     */
    public function __construct(array $errors, int $directories, int $files, int $linesOfCode, int $commentLinesOfCode, int $nonCommentLinesOfCode, int $logicalLinesOfCode, int $functions, int $lowestCyclomaticComplexityForFunction, float $averageCyclomaticComplexityForFunction, int $highestCyclomaticComplexityForFunction, int $classesOrTraits, int $methods, int $lowestCyclomaticComplexityForMethod, float $averageCyclomaticComplexityForMethod, int $highestCyclomaticComplexityForMethod)
    {
        $this->errors                                 = $errors;
        $this->directories                            = $directories;
        $this->files                                  = $files;
        $this->linesOfCode                            = $linesOfCode;
        $this->commentLinesOfCode                     = $commentLinesOfCode;
        $this->nonCommentLinesOfCode                  = $nonCommentLinesOfCode;
        $this->logicalLinesOfCode                     = $logicalLinesOfCode;
        $this->functions                              = $functions;
        $this->lowestCyclomaticComplexityForFunction  = $lowestCyclomaticComplexityForFunction;
        $this->averageCyclomaticComplexityForFunction = $averageCyclomaticComplexityForFunction;
        $this->highestCyclomaticComplexityForFunction = $highestCyclomaticComplexityForFunction;
        $this->classesOrTraits                        = $classesOrTraits;
        $this->methods                                = $methods;
        $this->lowestCyclomaticComplexityForMethod    = $lowestCyclomaticComplexityForMethod;
        $this->averageCyclomaticComplexityForMethod   = $averageCyclomaticComplexityForMethod;
        $this->highestCyclomaticComplexityForMethod   = $highestCyclomaticComplexityForMethod;
    }

    /**
     * @phpstan-assert-if-true non-empty-list<non-empty-string> $this->errors
     */
    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }

    /**
     * @return list<non-empty-string>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @return non-negative-int
     */
    public function directories(): int
    {
        return $this->directories;
    }

    /**
     * @return non-negative-int
     */
    public function files(): int
    {
        return $this->files;
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
        if ($this->linesOfCode() === 0) {
            return 0.0;
        }

        return ($this->commentLinesOfCode() / $this->linesOfCode()) * 100;
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
        if ($this->linesOfCode() === 0) {
            return 0.0;
        }

        return ($this->nonCommentLinesOfCode() / $this->linesOfCode()) * 100;
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
        if ($this->linesOfCode() === 0) {
            return 0.0;
        }

        return ($this->logicalLinesOfCode() / $this->linesOfCode()) * 100;
    }

    /**
     * @return non-negative-int
     */
    public function functions(): int
    {
        return $this->functions;
    }

    /**
     * @return non-negative-int
     */
    public function lowestCyclomaticComplexityForFunction(): int
    {
        return $this->lowestCyclomaticComplexityForFunction;
    }

    public function averageCyclomaticComplexityForFunction(): float
    {
        return $this->averageCyclomaticComplexityForFunction;
    }

    /**
     * @return non-negative-int
     */
    public function highestCyclomaticComplexityForFunction(): int
    {
        return $this->highestCyclomaticComplexityForFunction;
    }

    /**
     * @return non-negative-int
     */
    public function methods(): int
    {
        return $this->methods;
    }

    /**
     * @return non-negative-int
     */
    public function lowestCyclomaticComplexityForMethod(): int
    {
        return $this->lowestCyclomaticComplexityForMethod;
    }

    public function averageCyclomaticComplexityForMethod(): float
    {
        return $this->averageCyclomaticComplexityForMethod;
    }

    /**
     * @return non-negative-int
     */
    public function highestCyclomaticComplexityForMethod(): int
    {
        return $this->highestCyclomaticComplexityForMethod;
    }

    /**
     * @return non-negative-int
     */
    public function classesOrTraits(): int
    {
        return $this->classesOrTraits;
    }
}
