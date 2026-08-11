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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ArgumentsBuilder::class)]
#[UsesClass(Arguments::class)]
#[UsesClass(ArgumentsBuilderException::class)]
#[Small]
final class ArgumentsBuilderTest extends TestCase
{
    public function testHasDefaults(): void
    {
        $arguments = (new ArgumentsBuilder)->build(['phploc', 'src']);

        $this->assertSame(['src'], $arguments->directories());
        $this->assertSame(['.php'], $arguments->suffixes());
        $this->assertSame([], $arguments->exclude());
        $this->assertFalse($arguments->debug());
        $this->assertFalse($arguments->help());
        $this->assertFalse($arguments->version());
    }

    public function testMultipleDirectoriesCanBeSpecified(): void
    {
        $arguments = (new ArgumentsBuilder)->build(['phploc', 'src', 'tests']);

        $this->assertSame(['src', 'tests'], $arguments->directories());
    }

    public function testSuffixesAreAddedToTheDefaultSuffix(): void
    {
        $arguments = (new ArgumentsBuilder)->build(
            ['phploc', '--suffix', '.lib', '--suffix', '.inc', 'src'],
        );

        $this->assertSame(['.php', '.lib', '.inc'], $arguments->suffixes());
    }

    public function testPathsCanBeExcluded(): void
    {
        $arguments = (new ArgumentsBuilder)->build(
            ['phploc', '--exclude', 'vendor', '--exclude', 'build', 'src'],
        );

        $this->assertSame(['vendor', 'build'], $arguments->exclude());
    }

    public function testDebugCanBeEnabled(): void
    {
        $arguments = (new ArgumentsBuilder)->build(['phploc', '--debug', 'src']);

        $this->assertTrue($arguments->debug());
    }

    public function testHelpCanBeRequestedUsingLongOption(): void
    {
        $arguments = (new ArgumentsBuilder)->build(['phploc', '--help']);

        $this->assertTrue($arguments->help());
    }

    public function testHelpCanBeRequestedUsingShortOption(): void
    {
        $arguments = (new ArgumentsBuilder)->build(['phploc', '-h']);

        $this->assertTrue($arguments->help());
    }

    public function testVersionCanBeRequestedUsingLongOption(): void
    {
        $arguments = (new ArgumentsBuilder)->build(['phploc', '--version']);

        $this->assertTrue($arguments->version());
    }

    public function testVersionCanBeRequestedUsingShortOption(): void
    {
        $arguments = (new ArgumentsBuilder)->build(['phploc', '-v']);

        $this->assertTrue($arguments->version());
    }

    public function testRejectsMissingDirectory(): void
    {
        $this->expectException(ArgumentsBuilderException::class);
        $this->expectExceptionMessage('No directory specified');

        (new ArgumentsBuilder)->build(['phploc']);
    }

    public function testRejectsUnknownOption(): void
    {
        $this->expectException(ArgumentsBuilderException::class);
        $this->expectExceptionMessage('Unknown option "--unknown"');

        (new ArgumentsBuilder)->build(['phploc', '--unknown', 'src']);
    }
}
