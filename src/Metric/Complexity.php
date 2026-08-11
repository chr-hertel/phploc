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
 * The "Cyclomatic Complexity" section of the report.
 *
 * The statistics for classes cover classes, traits, and enums; the statistics
 * for methods and functions cover only those that have a body, so abstract and
 * interface methods are not part of them.
 */
final readonly class Complexity
{
    public function __construct(
        private float $averagePerLogicalLine,
        private Statistics $classes,
        private Statistics $methods,
        private Statistics $functions,
    ) {
    }

    public function averagePerLogicalLine(): float
    {
        return $this->averagePerLogicalLine;
    }

    public function classes(): Statistics
    {
        return $this->classes;
    }

    public function methods(): Statistics
    {
        return $this->methods;
    }

    public function functions(): Statistics
    {
        return $this->functions;
    }
}
