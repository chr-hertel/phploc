<?php

/*
 * This file is part of PHPLOC.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

uses(\PHPUnit\Framework\TestCase::class);

test('it adds numbers', function (): void {
    expect(1 + 1)->toBe(2);
});

it('subtracts numbers', function (): void {
    expect(2 - 1)->toBe(1);
});
