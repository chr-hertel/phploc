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
 * The "Tests" section of the report.
 *
 * Test classes and test methods are always reported here instead of being
 * measured as production code. For Pest, which has no test classes, each file
 * that declares tests counts as one test class and each test() / it() call as
 * one test method.
 */
final readonly class Tests
{
    /**
     * @param non-negative-int $classes
     * @param non-negative-int $methods
     */
    public function __construct(
        private int $classes,
        private int $methods,
    ) {
    }

    /**
     * @return non-negative-int
     */
    public function classes(): int
    {
        return $this->classes;
    }

    /**
     * @return non-negative-int
     */
    public function methods(): int
    {
        return $this->methods;
    }
}
