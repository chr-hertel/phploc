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
 * Minimum, average, and maximum of a set of measurements.
 */
final readonly class Statistics
{
    /**
     * @param non-negative-int $minimum
     * @param non-negative-int $maximum
     */
    public function __construct(
        private int $minimum,
        private float $average,
        private int $maximum,
    ) {
    }

    /**
     * @param list<non-negative-int> $values
     */
    public static function fromValues(array $values): self
    {
        if ([] === $values) {
            return new self(0, 0.0, 0);
        }

        return new self(
            min($values),
            array_sum($values) / \count($values),
            max($values),
        );
    }

    /**
     * @return non-negative-int
     */
    public function minimum(): int
    {
        return $this->minimum;
    }

    public function average(): float
    {
        return $this->average;
    }

    /**
     * @return non-negative-int
     */
    public function maximum(): int
    {
        return $this->maximum;
    }
}
