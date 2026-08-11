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

final readonly class JsonResultFormatter
{
    /**
     * @return non-empty-string
     *
     * @throws \JsonException
     */
    public function format(Result $result): string
    {
        $json = \json_encode(
            [
                'directories' => $result->directories(),
                'files' => $result->files(),
                'linesOfCode' => [
                    'total' => $result->linesOfCode(),
                    'comment' => $result->commentLinesOfCode(),
                    'commentPercentage' => $result->commentLinesOfCodePercentage(),
                    'nonComment' => $result->nonCommentLinesOfCode(),
                    'nonCommentPercentage' => $result->nonCommentLinesOfCodePercentage(),
                    'logical' => $result->logicalLinesOfCode(),
                    'logicalPercentage' => $result->logicalLinesOfCodePercentage(),
                ],
                'classesOrTraits' => $result->classesOrTraits(),
                'methods' => [
                    'count' => $result->methods(),
                    'cyclomaticComplexity' => [
                        'lowest' => $result->lowestCyclomaticComplexityForMethod(),
                        'average' => $result->averageCyclomaticComplexityForMethod(),
                        'highest' => $result->highestCyclomaticComplexityForMethod(),
                    ],
                ],
                'functions' => [
                    'count' => $result->functions(),
                    'cyclomaticComplexity' => [
                        'lowest' => $result->lowestCyclomaticComplexityForFunction(),
                        'average' => $result->averageCyclomaticComplexityForFunction(),
                        'highest' => $result->highestCyclomaticComplexityForFunction(),
                    ],
                ],
                'errors' => $result->errors(),
            ],
            \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_INVALID_UTF8_SUBSTITUTE | \JSON_PRESERVE_ZERO_FRACTION | \JSON_THROW_ON_ERROR,
        );

        return $json.\PHP_EOL;
    }
}
