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

use Hertel\PhpLoc\Metric\Complexity;
use Hertel\PhpLoc\Metric\Dependencies;
use Hertel\PhpLoc\Metric\Size;
use Hertel\PhpLoc\Metric\Statistics;
use Hertel\PhpLoc\Metric\Structure;
use Hertel\PhpLoc\Metric\Tests;

/**
 * The results that the formatter tests are run against.
 *
 * Every metric has a distinct value so that a formatter that mixes two of them
 * up cannot go unnoticed.
 */
final readonly class ResultFixture
{
    public static function result(): Result
    {
        return self::build();
    }

    public static function resultWithErrors(): Result
    {
        return self::build([
            'Cannot parse /path/to/First.php: Syntax error',
            'Cannot parse /path/to/Second.php: Syntax error',
        ]);
    }

    public static function emptyResult(): Result
    {
        return new Result(
            [],
            0,
            0,
            new Size(0, 0, 0, 0, 0, 0, 0, Statistics::fromValues([]), Statistics::fromValues([]), Statistics::fromValues([]), 0.0),
            new Complexity(0.0, Statistics::fromValues([]), Statistics::fromValues([]), Statistics::fromValues([])),
            new Dependencies(0, 0, 0, 0, 0, 0, 0),
            new Structure(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
            new Tests(0, 0),
        );
    }

    /**
     * @param list<non-empty-string> $errors
     */
    private static function build(array $errors = []): Result
    {
        return new Result(
            $errors,
            3,
            42,
            new Size(
                1520,
                320,
                1200,
                400,
                380,
                15,
                5,
                new Statistics(4, 38.5, 92),
                new Statistics(1, 6.25, 24),
                new Statistics(0, 5.5, 19),
                7.5,
            ),
            new Complexity(
                0.35,
                new Statistics(1, 5.2, 31),
                new Statistics(1, 2.4, 12),
                new Statistics(1, 3.75, 9),
            ),
            new Dependencies(4, 2, 1, 120, 8, 300, 45),
            new Structure(3, 1, 2, 4, 5, 20, 6, 60, 4, 40, 10, 14, 2, 3, 1, 7, 2),
            new Tests(12, 88),
        );
    }
}
