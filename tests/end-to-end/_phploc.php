<?php declare(strict_types=1);
/*
 * This file is part of PHPLOC.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Runs the phploc CLI in a subprocess and passes its output (including the
 * output written to STDERR by Symfony Console) through to this process.
 */
function phploc(string ...$arguments): void
{
    $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__DIR__ . '/../../phploc');

    foreach ($arguments as $argument) {
        $command .= ' ' . escapeshellarg($argument);
    }

    passthru($command . ' 2>&1');
}
