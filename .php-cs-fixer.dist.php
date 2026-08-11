<?php declare(strict_types=1);

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$header = <<<'EOF'
This file is part of PHPLOC.

(c) Sebastian Bergmann <sebastian@phpunit.de>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

$finder = PhpCsFixer\Finder::create()
    ->files()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests/_helper')
    ->in(__DIR__ . '/tests/unit')
    ->append([__DIR__ . '/phploc']);

$config = new PhpCsFixer\Config;
$config->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
    ]);

return $config;
