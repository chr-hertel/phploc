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

use function array_sum;
use function array_unique;
use function assert;
use function count;
use function dirname;
use function explode;
use function file_get_contents;
use function max;
use function min;
use function sprintf;
use function substr_count;
use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use SebastianBergmann\Complexity\ComplexityCalculatingVisitor;
use SebastianBergmann\Complexity\ComplexityCollection;
use SebastianBergmann\LinesOfCode\LineCountingVisitor;
use SebastianBergmann\LinesOfCode\LinesOfCode;

final class Analyser
{
    /**
     * @param list<non-empty-string> $files
     */
    public function analyse(array $files): Result
    {
        $errors      = [];
        $directories = [];
        $complexity  = ComplexityCollection::fromList();
        $linesOfCode = null;

        foreach ($files as $file) {
            $directories[] = dirname($file);

            try {
                $result = $this->analyseFile($file);

                $complexity = $complexity->mergeWith($result['complexity']);

                if ($result['linesOfCode'] !== null) {
                    $linesOfCode = $linesOfCode === null ? $result['linesOfCode'] : $linesOfCode->plus($result['linesOfCode']);
                }
            } catch (ParserException $e) {
                $message = $e->getMessage();

                assert($message !== '');

                $errors[] = $message;
            }
        }

        $classesOrTraits = [];

        foreach ($complexity->isMethod() as $item) {
            $classesOrTraits[] = explode('::', $item->name())[0];
        }

        $classesOrTraits     = count(array_unique($classesOrTraits));
        $complexityFunctions = $complexity->isFunction();
        $numberOfFunctions   = $complexityFunctions->count();
        $complexityFunctions = $this->cyclomaticComplexityStatistics($complexityFunctions);
        $complexityMethods   = $complexity->isMethod();
        $numberOfMethods     = $complexityMethods->count();
        $complexityMethods   = $this->cyclomaticComplexityStatistics($complexityMethods);

        return new Result(
            $errors,
            count(array_unique($directories)),
            count($files),
            $linesOfCode?->linesOfCode() ?? 0,
            $linesOfCode?->commentLinesOfCode() ?? 0,
            $linesOfCode?->nonCommentLinesOfCode() ?? 0,
            $linesOfCode?->logicalLinesOfCode() ?? 0,
            $numberOfFunctions,
            $complexityFunctions['minimum'],
            $complexityFunctions['average'],
            $complexityFunctions['maximum'],
            $classesOrTraits,
            $numberOfMethods,
            $complexityMethods['minimum'],
            $complexityMethods['average'],
            $complexityMethods['maximum'],
        );
    }

    /**
     * @param non-empty-string $file
     *
     * @throws ParserException
     *
     * @return array{complexity: ComplexityCollection, linesOfCode: ?LinesOfCode}
     */
    private function analyseFile(string $file): array
    {
        $source = file_get_contents($file);

        if ($source === false) {
            throw new ParserException(
                sprintf(
                    'Cannot read %s',
                    $file,
                ),
            );
        }

        if ($source === '') {
            return [
                'complexity'  => ComplexityCollection::fromList(),
                'linesOfCode' => null,
            ];
        }

        $parser = $this->parser();
        $lines  = substr_count($source, "\n");

        if ($lines === 0) {
            $lines = 1;
        }

        try {
            $nodes = $parser->parse($source);

            assert($nodes !== null);

            $traverser = new NodeTraverser;

            $complexityCalculatingVisitor = new ComplexityCalculatingVisitor(false);
            $lineCountingVisitor          = new LineCountingVisitor($lines);

            $traverser->addVisitor(new NameResolver);
            $traverser->addVisitor(new ParentConnectingVisitor);
            $traverser->addVisitor($complexityCalculatingVisitor);
            $traverser->addVisitor($lineCountingVisitor);

            $traverser->traverse($nodes);
        } catch (Error $error) {
            throw new ParserException(
                sprintf(
                    'Cannot parse %s: %s',
                    $file,
                    $error->getMessage(),
                ),
                $error->getCode(),
                $error,
            );
        }

        return [
            'complexity'  => $complexityCalculatingVisitor->result(),
            'linesOfCode' => $lineCountingVisitor->result(),
        ];
    }

    private function parser(): Parser
    {
        return (new ParserFactory)->createForNewestSupportedVersion();
    }

    /**
     * @return array{minimum: non-negative-int, maximum: non-negative-int, average: float}
     */
    private function cyclomaticComplexityStatistics(ComplexityCollection $items): array
    {
        $values = [];

        foreach ($items as $item) {
            $values[] = $item->cyclomaticComplexity();
        }

        return [
            'minimum' => !empty($values) ? min($values) : 0,
            'maximum' => !empty($values) ? max($values) : 0,
            'average' => !empty($values) ? array_sum($values) / count($values) : 0,
        ];
    }
}
