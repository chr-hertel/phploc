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

/**
 * A class-like or method the MetricsVisitor is currently inside of.
 *
 * Logical lines are attributed to the innermost scope that contains them, so
 * that the logical lines of all scopes add up to the logical lines of the file.
 *
 * @internal
 */
final class Scope
{
    /**
     * @var non-negative-int
     */
    public int $logicalLines = 0;

    /**
     * @param non-negative-int $complexity
     * @param non-negative-int $methods
     */
    public function __construct(
        public readonly int $complexity = 0,
        public readonly int $methods = 0,
        public readonly bool $includeInStatistics = false,
    ) {
    }
}
