<?php
/*
 * This file is part of the NEO ERP Application.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         Nur Wachid (nur@wach.id)
 * @Copyright (c) PT. Lingkar Kreasi (Circle Creative) 2022 .
 */

/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.1.0|configurator
 * you can change this configuration by importing this file.
 */
$project_path = getcwd();
$config = new PhpCsFixer\Config();
return $config
    ->setRules([
        '@PSR2'                                            => true,
        'nullable_type_declaration_for_default_null_value' => [
            'use_nullable_type_declaration' => true
        ],
        'array_syntax'                                     => [
            'syntax' => 'short'
        ],
        'ordered_imports'                                  => [
            'sort_algorithm' => 'length'
        ],
        'blank_line_before_statement'                      => [
            'statements' => ['break', 'case', 'continue', 'declare', 'default', 'do', 'exit', 'for', 'foreach', 'goto', 'if', 'include', 'include_once', 'require', 'require_once', 'return', 'switch', 'throw', 'try', 'while', 'yield', 'yield_from'],
        ],
        'method_argument_space'                            => [
            'on_multiline'                     => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => true,
        ],
        'no_extra_blank_lines'                             => [
            'tokens' => ['break', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'switch', 'throw', 'use', 'use_trait'],
        ],
        'cast_spaces'                                      => [
            'space' => 'single'
        ],
        'phpdoc_single_line_var_spacing'                   => true,
        'phpdoc_var_without_name'                          => true,
        'single_space_after_construct'                     => true,
        'single_line_after_imports'                        => true,
        'no_unused_imports'                                => true,
        'not_operator_with_successor_space'                => true,
        'trailing_comma_in_multiline'                      => ['elements' => ['arrays']],
        'phpdoc_scalar'                                    => true,
        'unary_operator_spaces'                            => true,
        'binary_operator_spaces'                           => ['operators' => ['=>' => 'align']],
        'single_trait_insert_per_statement'                => false,
        'method_chaining_indentation'                      => true,
        'array_indentation'                                => true,
        'single_quote'                                     => true,
        'no_singleline_whitespace_before_semicolons'       => true,
        'no_empty_statement'                               => true,
        'standardize_increment'                            => true,
        'object_operator_without_whitespace'               => true,
        'ternary_operator_spaces'                          => true,
        'no_leading_namespace_whitespace'                  => true,
        'no_blank_lines_before_namespace'                  => true,
        'blank_line_after_namespace'                       => true,
        'fully_qualified_strict_types'                     => true,
        'single_line_throw'                                => true,
        'function_typehint_space'                          => true,
        'simplified_if_return'                             => true,
        'no_useless_else'                                  => true,
        'no_unneeded_curly_braces'                         => true,
        'no_empty_comment'                                 => true,
        'no_blank_lines_after_class_opening'               => true,
        'whitespace_after_comma_in_array'                  => true,
        'trim_array_spaces'                                => true,
        'no_whitespace_before_comma_in_array'              => true,
        'constant_case'                                    => true,
        'lowercase_keywords'                               => true,
        'lowercase_static_reference'                       => true,
        'lambda_not_used_import'                           => true,
    ])
    ->setFinder(PhpCsFixer\Finder::create()
        ->exclude('vendor')
        ->in([
            $project_path . '/src',
            $project_path . '/config',
            $project_path . '/tests',
        ])
        ->ignoreDotFiles(true)
        ->ignoreVCS(true)
    )
;
