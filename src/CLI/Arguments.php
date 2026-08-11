<?php declare(strict_types=1);
/*
 * This file is part of PHPLOC.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPLOC;

final class Arguments
{
    /**
     * @var list<non-empty-string>
     */
    private array $directories;

    /**
     * @var list<non-empty-string>
     */
    private array $suffixes;

    /**
     * @var list<non-empty-string>
     */
    private array $exclude;
    private bool $debug;
    private bool $help;
    private bool $version;

    /**
     * @param list<non-empty-string> $directories
     * @param list<non-empty-string> $suffixes
     * @param list<non-empty-string> $exclude
     */
    public function __construct(array $directories, array $suffixes, array $exclude, bool $debug, bool $help, bool $version)
    {
        $this->directories = $directories;
        $this->suffixes    = $suffixes;
        $this->exclude     = $exclude;
        $this->debug       = $debug;
        $this->help        = $help;
        $this->version     = $version;
    }

    /**
     * @return list<non-empty-string>
     */
    public function directories(): array
    {
        return $this->directories;
    }

    /**
     * @return list<non-empty-string>
     */
    public function suffixes(): array
    {
        return $this->suffixes;
    }

    /**
     * @return list<non-empty-string>
     */
    public function exclude(): array
    {
        return $this->exclude;
    }

    public function debug(): bool
    {
        return $this->debug;
    }

    public function help(): bool
    {
        return $this->help;
    }

    public function version(): bool
    {
        return $this->version;
    }
}
