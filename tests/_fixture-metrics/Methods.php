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

class ExampleMethods
{
    public function publicMethod(): void
    {
    }

    protected function protectedMethod(): void
    {
    }

    private function privateMethod(): void
    {
    }

    public static function publicStaticMethod(): void
    {
    }

    private static function privateStaticMethod(): void
    {
    }

    function methodWithoutVisibility(): void
    {
    }
}

abstract class AbstractExampleMethods
{
    abstract public function abstractMethod(): void;
}

interface ExampleMethodsInterface
{
    public function interfaceMethod(): void;
}
