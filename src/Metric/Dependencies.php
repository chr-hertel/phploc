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
 * The "Dependencies" section of the report.
 */
final readonly class Dependencies
{
    /**
     * @param non-negative-int $globalConstantAccesses
     * @param non-negative-int $globalVariableAccesses
     * @param non-negative-int $superGlobalVariableAccesses
     * @param non-negative-int $nonStaticAttributeAccesses
     * @param non-negative-int $staticAttributeAccesses
     * @param non-negative-int $nonStaticMethodCalls
     * @param non-negative-int $staticMethodCalls
     */
    public function __construct(
        private int $globalConstantAccesses,
        private int $globalVariableAccesses,
        private int $superGlobalVariableAccesses,
        private int $nonStaticAttributeAccesses,
        private int $staticAttributeAccesses,
        private int $nonStaticMethodCalls,
        private int $staticMethodCalls,
    ) {
    }

    /**
     * @return non-negative-int
     */
    public function globalAccesses(): int
    {
        return $this->globalConstantAccesses + $this->globalVariableAccesses + $this->superGlobalVariableAccesses;
    }

    /**
     * @return non-negative-int
     */
    public function globalConstantAccesses(): int
    {
        return $this->globalConstantAccesses;
    }

    public function globalConstantAccessesPercentage(): float
    {
        return $this->percentage($this->globalConstantAccesses, $this->globalAccesses());
    }

    /**
     * @return non-negative-int
     */
    public function globalVariableAccesses(): int
    {
        return $this->globalVariableAccesses;
    }

    public function globalVariableAccessesPercentage(): float
    {
        return $this->percentage($this->globalVariableAccesses, $this->globalAccesses());
    }

    /**
     * @return non-negative-int
     */
    public function superGlobalVariableAccesses(): int
    {
        return $this->superGlobalVariableAccesses;
    }

    public function superGlobalVariableAccessesPercentage(): float
    {
        return $this->percentage($this->superGlobalVariableAccesses, $this->globalAccesses());
    }

    /**
     * @return non-negative-int
     */
    public function attributeAccesses(): int
    {
        return $this->nonStaticAttributeAccesses + $this->staticAttributeAccesses;
    }

    /**
     * @return non-negative-int
     */
    public function nonStaticAttributeAccesses(): int
    {
        return $this->nonStaticAttributeAccesses;
    }

    public function nonStaticAttributeAccessesPercentage(): float
    {
        return $this->percentage($this->nonStaticAttributeAccesses, $this->attributeAccesses());
    }

    /**
     * @return non-negative-int
     */
    public function staticAttributeAccesses(): int
    {
        return $this->staticAttributeAccesses;
    }

    public function staticAttributeAccessesPercentage(): float
    {
        return $this->percentage($this->staticAttributeAccesses, $this->attributeAccesses());
    }

    /**
     * @return non-negative-int
     */
    public function methodCalls(): int
    {
        return $this->nonStaticMethodCalls + $this->staticMethodCalls;
    }

    /**
     * @return non-negative-int
     */
    public function nonStaticMethodCalls(): int
    {
        return $this->nonStaticMethodCalls;
    }

    public function nonStaticMethodCallsPercentage(): float
    {
        return $this->percentage($this->nonStaticMethodCalls, $this->methodCalls());
    }

    /**
     * @return non-negative-int
     */
    public function staticMethodCalls(): int
    {
        return $this->staticMethodCalls;
    }

    public function staticMethodCallsPercentage(): float
    {
        return $this->percentage($this->staticMethodCalls, $this->methodCalls());
    }

    /**
     * @param non-negative-int $value
     * @param non-negative-int $total
     */
    private function percentage(int $value, int $total): float
    {
        if (0 === $total) {
            return 0.0;
        }

        return ($value / $total) * 100;
    }
}
