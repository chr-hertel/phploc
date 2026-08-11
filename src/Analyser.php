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

use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use SebastianBergmann\LinesOfCode\LineCountingVisitor;

final class Analyser
{
    /**
     * @param list<non-empty-string> $files
     */
    public function analyse(array $files): Result
    {
        $collector = new Collector();
        $tests = $this->detectTests($files);

        $errors = [];
        $directories = [];

        foreach ($files as $file) {
            $directories[] = \dirname($file);

            try {
                $this->analyseFile($file, $collector, $tests);
            } catch (ParserException $e) {
                $message = $e->getMessage();

                \assert('' !== $message);

                $errors[] = $message;
            }
        }

        return $collector->result(
            $errors,
            \count(array_unique($directories)),
            \count($files),
        );
    }

    /**
     * Files that cannot be read or parsed are ignored here; the measuring pass
     * reports them as errors.
     *
     * @param list<non-empty-string> $files
     */
    private function detectTests(array $files): TestClassRegistry
    {
        $registry = new TestClassRegistry();
        $parser = $this->parser();

        foreach ($files as $file) {
            $source = file_get_contents($file);

            if (false === $source || '' === $source || !TestClassRegistry::couldDeclareTests($source)) {
                continue;
            }

            try {
                $nodes = $parser->parse($source);
            } catch (Error) {
                continue;
            }

            if (null === $nodes) {
                continue;
            }

            $traverser = new NodeTraverser();

            $traverser->addVisitor(new NameResolver());
            $traverser->addVisitor(new TestDetectionVisitor($registry, $file));

            $traverser->traverse($nodes);
        }

        return $registry;
    }

    /**
     * @param non-empty-string $file
     *
     * @throws ParserException
     */
    private function analyseFile(string $file, Collector $collector, TestClassRegistry $tests): void
    {
        $source = file_get_contents($file);

        if (false === $source) {
            throw new ParserException(\sprintf('Cannot read %s', $file));
        }

        if ('' === $source) {
            return;
        }

        $parser = $this->parser();
        $lines = substr_count($source, "\n");

        if (0 === $lines) {
            $lines = 1;
        }

        try {
            $nodes = $parser->parse($source);

            \assert(null !== $nodes);

            $traverser = new NodeTraverser();

            $lineCountingVisitor = new LineCountingVisitor($lines);

            $traverser->addVisitor(new NameResolver());
            $traverser->addVisitor($lineCountingVisitor);
            $traverser->addVisitor(
                new MetricsVisitor($collector, $tests, $tests->isPestFile($file)),
            );

            $traverser->traverse($nodes);
        } catch (Error $error) {
            throw new ParserException(\sprintf('Cannot parse %s: %s', $file, $error->getMessage()), $error->getCode(), $error);
        }

        $linesOfCode = $lineCountingVisitor->result();

        $collector->addLines(
            $linesOfCode->linesOfCode(),
            $linesOfCode->commentLinesOfCode(),
            $linesOfCode->nonCommentLinesOfCode(),
        );
    }

    private function parser(): Parser
    {
        return (new ParserFactory())->createForNewestSupportedVersion();
    }
}
