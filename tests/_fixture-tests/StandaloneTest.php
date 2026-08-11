<?php

/*
 * This file is part of PHPLOC.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hertel\PhpLoc\TestFixture;

// This class extends nothing, so it is only recognised as a test class by the
// fallback on its name -- and the file has to survive the prefilter of the
// test detection pass to get that far.
final class StandaloneTest
{
    public function testSomething(): void
    {
        $value = 1;
    }
}
