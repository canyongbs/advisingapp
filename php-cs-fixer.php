<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    '@PSR12' => true,
    'phpdoc_no_empty_return' => false,
    'phpdoc_var_annotation_correct_order' => true,
    'array_syntax' => [
        'syntax' => 'short',
    ],
    'no_singleline_whitespace_before_semicolons' => true,
    'no_extra_blank_lines' => [
        'tokens' => [
            'break', 'continue', 'curly_brace_block', 'default',
            'extra', 'parenthesis_brace_block', 'return',
            'square_brace_block', 'switch', 'throw', 'use',
        ],
    ],
    'cast_spaces' => [
        'space' => 'single',
    ],
    'concat_space' => [
        'spacing' => 'one',
    ],
    'ordered_imports' => [
        'sort_algorithm' => 'length',
    ],
    'single_quote' => true,
    'lowercase_cast' => true,
    'lowercase_static_reference' => true,
    'no_empty_phpdoc' => true,
    'no_empty_comment' => true,
    'array_indentation' => true,
    'short_scalar_cast' => true,
    'class_attributes_separation' => [
        'elements' => ['const' => 'one', 'method' => 'one', 'property' => 'one', 'trait_import' => 'none'],
    ],
    'no_mixed_echo_print' => [
        'use' => 'echo',
    ],
    'no_unused_imports' => true,
    'binary_operator_spaces' => [
        'default' => 'single_space',
    ],
    'no_empty_statement' => true,
    'unary_operator_spaces' => true, // $number ++ becomes $number++
    'single_line_comment_style' => ['comment_types' => ['hash']], // # becomes //
    'standardize_not_equals' => true, // <> becomes !=
    'native_function_casing' => true,
    'ternary_operator_spaces' => true,
    'ternary_to_null_coalescing' => true,
    'declare_equal_normalize' => [
        'space' => 'single',
    ],
    'type_declaration_spaces' => true,
    'no_leading_import_slash' => true,
    'blank_line_before_statement' => [
        'statements' => [
            'break', 'continue',
            'declare', 'default', 'exit',
            'do', 'for', 'foreach', 'goto',
            'if', 'include', 'include_once',
            'require', 'require_once', 'return',
            'switch', 'throw', 'try', 'while', 'yield',
        ],
    ],
    'combine_consecutive_unsets' => true,
    'method_chaining_indentation' => true,
    'no_whitespace_in_blank_line' => true,
    'blank_line_after_opening_tag' => true,
    'list_syntax' => ['syntax' => 'short'],
    // public function getTimezoneAttribute( ? Banana $value) becomes public function getTimezoneAttribute(?Banana $value)
    'compact_nullable_typehint' => true,
    'explicit_string_variable' => true,
    'no_leading_namespace_whitespace' => true,
    'trailing_comma_in_multiline' => true,
    'not_operator_with_successor_space' => true,
    'object_operator_without_whitespace' => true,
    'no_blank_lines_after_class_opening' => true,
    'no_blank_lines_after_phpdoc' => true,
    'no_whitespace_before_comma_in_array' => true,
    'no_trailing_comma_in_singleline' => true,
    'multiline_whitespace_before_semicolons' => [
        'strategy' => 'no_multi_line',
    ],
    'no_multiline_whitespace_around_double_arrow' => true,
    'no_useless_return' => true,
    'phpdoc_add_missing_param_annotation' => true,
    'phpdoc_order' => true,
    'phpdoc_scalar' => true,
    'phpdoc_separation' => true,
    'phpdoc_single_line_var_spacing' => true,
    'single_trait_insert_per_statement' => true,
    'ordered_class_elements' => [
        'order' => [
            'use_trait',
            'constant',
            'property',
            'construct',
            'phpunit',
            'public',
            'protected',
            'private',
        ],
        'sort_algorithm' => 'none',
    ],
    'return_type_declaration' => [
        'space_before' => 'none',
    ],
    'constant_case' => [
        'case' => 'lower',
    ],
    'no_useless_else' => true,
    'no_spaces_around_offset' => true,
    'whitespace_after_comma_in_array' => true,
    'trim_array_spaces' => true,
    'single_space_around_construct' => true,
];

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude(
        [
            'bootstrap/cache',
            'storage',
            'vendor',
            'bower_components',
            'node_modules',
            '_ide_helper.php',
        ]
    )
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setIndent('    ')
    ->setLineEnding("\n")
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache')
    ->setRiskyAllowed(false)
    ->setRules($rules)
    ->setFinder($finder);
