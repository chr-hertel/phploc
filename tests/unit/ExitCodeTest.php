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

use const PHP_BINARY;
use const PHP_EOL;
use function escapeshellarg;
use function exec;
use function implode;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\TestCase;

/**
 * The .phpt tests assert the CLI's output; PHPUnit's .phpt runner silently
 * ignores --EXPECT_EXITCODE--, so the exit codes are asserted here.
 */
#[CoversNothing]
#[Medium]
final class ExitCodeTest extends TestCase
{
    public function testSucceedsWhenFilesCanBeAnalysed(): void
    {
        $this->assertSame(0, $this->runPhploc([__DIR__ . '/../_fixture']));
    }

    public function testSucceedsButReportsErrorsWhenFileCannotBeParsed(): void
    {
        $exitCode = $this->runPhploc([__DIR__ . '/../_fixture-invalid'], $output);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Errors:', $output);
        $this->assertStringContainsString('Cannot parse', $output);
    }

    public function testFailsWhenNoDirectoryIsSpecified(): void
    {
        $exitCode = $this->runPhploc([], $output);

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('Not enough arguments', $output);
    }

    public function testFailsWhenNoFilesAreFound(): void
    {
        $exitCode = $this->runPhploc([__DIR__ . '/../_fixture-that-does-not-exist'], $output);

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('No files found to scan', $output);
    }

    public function testFailsWhenAnUnknownOptionIsUsed(): void
    {
        $exitCode = $this->runPhploc(['--unknown', __DIR__ . '/../_fixture'], $output);

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('The "--unknown" option does not exist.', $output);
    }

    public function testSucceedsWhenHelpIsRequested(): void
    {
        $exitCode = $this->runPhploc(['--help'], $output);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Usage:', $output);
    }

    public function testSucceedsWhenVersionIsRequested(): void
    {
        $exitCode = $this->runPhploc(['--version'], $output);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('phploc', $output);
    }

    /**
     * @param list<non-empty-string> $arguments
     */
    private function runPhploc(array $arguments, ?string &$output = null): int
    {
        $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__DIR__ . '/../../phploc');

        foreach ($arguments as $argument) {
            $command .= ' ' . escapeshellarg($argument);
        }

        exec($command . ' 2>&1', $lines, $exitCode);

        $output = implode(PHP_EOL, $lines);

        return $exitCode;
    }
}
