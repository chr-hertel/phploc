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

final readonly class Result
{
    /**
     * @param list<non-empty-string> $errors
     * @param non-negative-int       $directories
     * @param non-negative-int       $files
     */
    public function __construct(
        private array $errors,
        private int $directories,
        private int $files,
        private Size $size,
        private Complexity $complexity,
        private Dependencies $dependencies,
        private Structure $structure,
        private Tests $tests,
    ) {
    }

    /**
     * @phpstan-assert-if-true non-empty-list<non-empty-string> $this->errors
     */
    public function hasErrors(): bool
    {
        return [] !== $this->errors;
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

    public function size(): Size
    {
        return $this->size;
    }

    public function complexity(): Complexity
    {
        return $this->complexity;
    }

    public function dependencies(): Dependencies
    {
        return $this->dependencies;
    }

    public function structure(): Structure
    {
        return $this->structure;
    }

    public function tests(): Tests
    {
        return $this->tests;
    }
}
