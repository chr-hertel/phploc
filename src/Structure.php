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
 * The "Structure" section of the report.
 *
 * Enums are reported separately and are not part of the class counts.
 * Anonymous classes are counted as concrete, non-final classes. Methods are
 * counted as they are declared, so abstract and interface methods are part of
 * the method counts even though they have no body.
 */
final readonly class Structure
{
    /**
     * @param non-negative-int $namespaces
     * @param non-negative-int $interfaces
     * @param non-negative-int $traits
     * @param non-negative-int $enums
     * @param non-negative-int $abstractClasses
     * @param non-negative-int $finalClasses
     * @param non-negative-int $nonFinalClasses
     * @param non-negative-int $nonStaticMethods
     * @param non-negative-int $staticMethods
     * @param non-negative-int $publicMethods
     * @param non-negative-int $protectedMethods
     * @param non-negative-int $privateMethods
     * @param non-negative-int $namedFunctions
     * @param non-negative-int $anonymousFunctions
     * @param non-negative-int $globalConstants
     * @param non-negative-int $publicClassConstants
     * @param non-negative-int $nonPublicClassConstants
     */
    public function __construct(
        private int $namespaces,
        private int $interfaces,
        private int $traits,
        private int $enums,
        private int $abstractClasses,
        private int $finalClasses,
        private int $nonFinalClasses,
        private int $nonStaticMethods,
        private int $staticMethods,
        private int $publicMethods,
        private int $protectedMethods,
        private int $privateMethods,
        private int $namedFunctions,
        private int $anonymousFunctions,
        private int $globalConstants,
        private int $publicClassConstants,
        private int $nonPublicClassConstants,
    ) {
    }

    /**
     * @return non-negative-int
     */
    public function namespaces(): int
    {
        return $this->namespaces;
    }

    /**
     * @return non-negative-int
     */
    public function interfaces(): int
    {
        return $this->interfaces;
    }

    /**
     * @return non-negative-int
     */
    public function traits(): int
    {
        return $this->traits;
    }

    /**
     * @return non-negative-int
     */
    public function enums(): int
    {
        return $this->enums;
    }

    /**
     * @return non-negative-int
     */
    public function classes(): int
    {
        return $this->abstractClasses + $this->concreteClasses();
    }

    /**
     * Classes, traits, and enums, i.e. everything that can declare methods.
     *
     * @return non-negative-int
     */
    public function classesOrTraits(): int
    {
        return $this->classes() + $this->traits + $this->enums;
    }

    /**
     * @return non-negative-int
     */
    public function abstractClasses(): int
    {
        return $this->abstractClasses;
    }

    public function abstractClassesPercentage(): float
    {
        return $this->percentage($this->abstractClasses, $this->classes());
    }

    /**
     * @return non-negative-int
     */
    public function concreteClasses(): int
    {
        return $this->finalClasses + $this->nonFinalClasses;
    }

    public function concreteClassesPercentage(): float
    {
        return $this->percentage($this->concreteClasses(), $this->classes());
    }

    /**
     * @return non-negative-int
     */
    public function finalClasses(): int
    {
        return $this->finalClasses;
    }

    public function finalClassesPercentage(): float
    {
        return $this->percentage($this->finalClasses, $this->concreteClasses());
    }

    /**
     * @return non-negative-int
     */
    public function nonFinalClasses(): int
    {
        return $this->nonFinalClasses;
    }

    public function nonFinalClassesPercentage(): float
    {
        return $this->percentage($this->nonFinalClasses, $this->concreteClasses());
    }

    /**
     * @return non-negative-int
     */
    public function methods(): int
    {
        return $this->nonStaticMethods + $this->staticMethods;
    }

    /**
     * @return non-negative-int
     */
    public function nonStaticMethods(): int
    {
        return $this->nonStaticMethods;
    }

    public function nonStaticMethodsPercentage(): float
    {
        return $this->percentage($this->nonStaticMethods, $this->methods());
    }

    /**
     * @return non-negative-int
     */
    public function staticMethods(): int
    {
        return $this->staticMethods;
    }

    public function staticMethodsPercentage(): float
    {
        return $this->percentage($this->staticMethods, $this->methods());
    }

    /**
     * @return non-negative-int
     */
    public function publicMethods(): int
    {
        return $this->publicMethods;
    }

    public function publicMethodsPercentage(): float
    {
        return $this->percentage($this->publicMethods, $this->methods());
    }

    /**
     * @return non-negative-int
     */
    public function protectedMethods(): int
    {
        return $this->protectedMethods;
    }

    public function protectedMethodsPercentage(): float
    {
        return $this->percentage($this->protectedMethods, $this->methods());
    }

    /**
     * @return non-negative-int
     */
    public function privateMethods(): int
    {
        return $this->privateMethods;
    }

    public function privateMethodsPercentage(): float
    {
        return $this->percentage($this->privateMethods, $this->methods());
    }

    /**
     * @return non-negative-int
     */
    public function functions(): int
    {
        return $this->namedFunctions + $this->anonymousFunctions;
    }

    /**
     * @return non-negative-int
     */
    public function namedFunctions(): int
    {
        return $this->namedFunctions;
    }

    public function namedFunctionsPercentage(): float
    {
        return $this->percentage($this->namedFunctions, $this->functions());
    }

    /**
     * @return non-negative-int
     */
    public function anonymousFunctions(): int
    {
        return $this->anonymousFunctions;
    }

    public function anonymousFunctionsPercentage(): float
    {
        return $this->percentage($this->anonymousFunctions, $this->functions());
    }

    /**
     * @return non-negative-int
     */
    public function constants(): int
    {
        return $this->globalConstants + $this->classConstants();
    }

    /**
     * @return non-negative-int
     */
    public function globalConstants(): int
    {
        return $this->globalConstants;
    }

    public function globalConstantsPercentage(): float
    {
        return $this->percentage($this->globalConstants, $this->constants());
    }

    /**
     * @return non-negative-int
     */
    public function classConstants(): int
    {
        return $this->publicClassConstants + $this->nonPublicClassConstants;
    }

    public function classConstantsPercentage(): float
    {
        return $this->percentage($this->classConstants(), $this->constants());
    }

    /**
     * @return non-negative-int
     */
    public function publicClassConstants(): int
    {
        return $this->publicClassConstants;
    }

    public function publicClassConstantsPercentage(): float
    {
        return $this->percentage($this->publicClassConstants, $this->classConstants());
    }

    /**
     * @return non-negative-int
     */
    public function nonPublicClassConstants(): int
    {
        return $this->nonPublicClassConstants;
    }

    public function nonPublicClassConstantsPercentage(): float
    {
        return $this->percentage($this->nonPublicClassConstants, $this->classConstants());
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
