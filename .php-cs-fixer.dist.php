<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PHPyh\CodingStandard\PhpCsFixerCodingStandard;

$finder = (new Finder())
    ->in(__DIR__)
    ->exclude([
        'var',
        'config',
    ])
    ->append([
        __FILE__,
        __DIR__ . '/bin/console',
    ]);

$config = (new Config())
    ->setFinder($finder);

(new PhpCsFixerCodingStandard())->applyTo($config);

return $config;
