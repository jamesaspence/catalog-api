<?php

use PhpCsFixer\Config;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/app')
;

$config = new Config();
return $config
    ->setUsingCache(false)
    ->setIndent("    ")
    ->setRules([
        '@PSR12' => true,
        '@PHP80Migration' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ternary_operator_spaces' => true,
        'trailing_comma_in_multiline' => true,
        'combine_consecutive_issets' => true,
        'no_unused_imports' => true,
        'binary_operator_spaces' => true,
    ])
    ->setFinder($finder);
