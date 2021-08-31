<?php

declare(strict_types=1);

return
    (new PhpCsFixer\Config())
        ->setFinder(
            PhpCsFixer\Finder::create()
                ->in([
                    __DIR__ . '/src',
                    __DIR__ . '/tests',
                ])
                ->append([
                    __FILE__,
                ])
        )
        ->setRules([
            '@PSR12' => true,
            '@PSR12:risky' => true,
            '@PHP74Migration' => true,
            '@PHP74Migration:risky' => true,
            '@PHPUnit84Migration:risky' => true,

            'no_unused_imports' => true,
            'ordered_imports' => ['imports_order' => ['class', 'function', 'const']],

            'no_superfluous_phpdoc_tags' => ['remove_inheritdoc' => true, 'allow_mixed' => true],

            'php_unit_internal_class' => true,
            'php_unit_construct' => true,
            'php_unit_fqcn_annotation' => true,
            'php_unit_set_up_tear_down_visibility' => true,
            'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],

            'global_namespace_import' => true,
        ]);
