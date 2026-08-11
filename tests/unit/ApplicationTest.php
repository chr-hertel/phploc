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

use function ob_get_clean;
use function ob_start;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Application::class)]
#[UsesClass(Analyser::class)]
#[UsesClass(Arguments::class)]
#[UsesClass(ArgumentsBuilder::class)]
#[UsesClass(ArgumentsBuilderException::class)]
#[UsesClass(Result::class)]
#[UsesClass(TextResultFormatter::class)]
#[Small]
final class ApplicationTest extends TestCase
{
    public function testSucceedsWhenFilesCanBeAnalysed(): void
    {
        $this->assertSame(0, $this->runApplication([__DIR__ . '/../_fixture']));
    }

    public function testSucceedsButReportsErrorsWhenFileCannotBeParsed(): void
    {
        $exitCode = $this->runApplication([__DIR__ . '/../_fixture-invalid'], $output);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Errors:', $output);
        $this->assertStringContainsString('Cannot parse', $output);
    }

    public function testFailsWhenNoDirectoryIsSpecified(): void
    {
        $this->assertSame(1, $this->runApplication([]));
    }

    public function testFailsWhenNoFilesAreFound(): void
    {
        $this->assertSame(1, $this->runApplication([__DIR__ . '/../_fixture-that-does-not-exist']));
    }

    public function testSucceedsWhenHelpIsRequested(): void
    {
        $exitCode = $this->runApplication(['--help'], $output);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Usage:', $output);
    }

    public function testSucceedsWhenVersionIsRequested(): void
    {
        $exitCode = $this->runApplication(['--version'], $output);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('by Sebastian Bergmann', $output);
    }

    /**
     * @param list<non-empty-string> $arguments
     */
    private function runApplication(array $arguments, ?string &$output = null): int
    {
        ob_start();

        $exitCode = (new Application)->run(['phploc', ...$arguments]);

        $output = (string) ob_get_clean();

        return $exitCode;
    }
}
