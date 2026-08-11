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

abstract class AbstractExampleClass
{
}

final class FinalExampleClass extends AbstractExampleClass
{
}

class NonFinalExampleClass extends AbstractExampleClass
{
}

interface ExampleClassInterface
{
}

trait ExampleClassTrait
{
}

enum ExampleClassEnum
{
    case One;
}

function exampleAnonymousClass(): object
{
    return new class {
        public function method(): void
        {
        }
    };
}
