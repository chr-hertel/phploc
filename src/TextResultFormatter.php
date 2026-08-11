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

final readonly class TextResultFormatter
{
    /**
     * @return non-empty-string
     */
    public function format(Result $result): string
    {
        $buffer = \sprintf(
            <<<'EOT'
Directories:                       %20s
Files:                             %20s

Lines of Code (LOC):               %20s
Comment Lines of Code (CLOC):      %20s (%.2f%%)
Non-Comment Lines of Code (NCLOC): %20s (%.2f%%)
Logical Lines of Code (LLOC):      %20s (%.2f%%)

EOT,
            \number_format($result->directories()),
            \number_format($result->files()),
            \number_format($result->linesOfCode()),
            \number_format($result->commentLinesOfCode()),
            $result->commentLinesOfCodePercentage(),
            \number_format($result->nonCommentLinesOfCode()),
            $result->nonCommentLinesOfCodePercentage(),
            \number_format($result->logicalLinesOfCode()),
            $result->logicalLinesOfCodePercentage(),
        );

        if ($result->classesOrTraits() > 0) {
            $buffer .= \sprintf(
                <<<'EOT'

Classes or Traits                  %20s
  Methods                          %20s
    Cyclomatic Complexity
      Lowest                       %20.2f
      Average                      %20.2f
      Highest                      %20.2f

EOT,
                \number_format($result->classesOrTraits()),
                \number_format($result->methods()),
                \number_format($result->lowestCyclomaticComplexityForMethod()),
                \number_format($result->averageCyclomaticComplexityForMethod()),
                \number_format($result->highestCyclomaticComplexityForMethod()),
            );
        }

        if ($result->functions() > 0) {
            $buffer .= \sprintf(
                <<<'EOT'

Functions                          %20s
  Cyclomatic Complexity
    Lowest                         %20.2f
    Average                        %20.2f
    Highest                        %20.2f

EOT,
                \number_format($result->functions()),
                \number_format($result->lowestCyclomaticComplexityForFunction()),
                \number_format($result->averageCyclomaticComplexityForFunction()),
                \number_format($result->highestCyclomaticComplexityForFunction()),
            );
        }

        if ($result->hasErrors()) {
            $buffer .= \PHP_EOL.'Errors:'.\PHP_EOL;

            foreach ($result->errors() as $error) {
                $buffer .= \sprintf(
                    '  %s'.\PHP_EOL,
                    $error,
                );
            }
        }

        return $buffer;
    }
}
