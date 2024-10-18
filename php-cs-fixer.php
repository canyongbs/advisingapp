<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

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
    'compact_nullable_type_declaration' => true,
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
    'fully_qualified_strict_types' => [
        'import_symbols' => true,
        'leading_backslash_in_global_namespace' => false,
    ],
    'global_namespace_import' => [
        'import_classes' => true,
        'import_constants' => true,
        'import_functions' => true,
    ],
    'single_line_empty_body' => true,
    'no_useless_nullsafe_operator' => true,
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
        ]
    )
    ->notPath(
        [
            '_ide_helper.php',
            '_ide_helper_models.php',
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
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setFinder($finder);
