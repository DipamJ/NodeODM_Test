<?php

/**
 * This configuration will be read and overlaid on top of the
 * default configuration. Command line arguments will be applied
 * after this file is read.
 *
 * @see src/Phan/Config.php
 * See Config for all configurable options.
 */
return [
    // Backwards Compatibility Checking. This is slow
    // and expensive, but you should consider running
    // it before upgrading your version of PHP to a
    // new version that has backward compatibility
    // breaks.
    //
    // If you are migrating from PHP 5 to PHP 7,
    // you should also look into using
    // [php7cc (no longer maintained)](https://github.com/sstalle/php7cc)
    // and [php7mar](https://github.com/Alexia/php7mar),
    // which have different backwards compatibility checks.
    //
    // If you are still using versions of php older than 5.6,
    // `PHP53CompatibilityPlugin` may be worth looking into if you are not running
    // syntax checks for php 5.3 through another method such as
    // `InvokePHPNativeSyntaxCheckPlugin` (see .phan/plugins/README.md).
    //
    // You may wish to disable 'redundant_condition_detection'
    // until your project drops php 5 support.
    'backward_compatibility_checks' => true,



    // A list of directories that should be parsed for class and
    // method information. After excluding the directories
    // defined in exclude_analysis_directory_list, the remaining
    // files will be statically analyzed for errors.
    //
    // Thus, both first-party and third-party code being used by
    // your application should be included in this list.
    'directory_list' => [
        //'data',
	//'uas_tools',
	'multi_users',
        //'vendor/symfony/console',
    ],
	/////////////////////////

    // A regex used to match every file name that you want to
    // exclude from parsing. Actual value will exclude every
    // "test", "tests", "Test" and "Tests" folders found in
    // "vendor/" directory.
    'exclude_file_regex' => '@^multi_users/PHPMailer@',

// Supported values: `'5.6'`, `'7.0'`, `'7.1'`, `'7.2'`, `'7.3'`,
    // `'7.4'`, `null`.
    // If this is set to `null`,
    // then Phan assumes the PHP version which is closest to the minor version
    // of the php executable used to execute Phan.
    //
    // Note that the **only** effect of choosing `'5.6'` is to infer
    // that functions removed in php 7.0 exist.
    // (See `backward_compatibility_checks` for additional options)
    // TODO: Set this.
    'target_php_version' => 7.3,

    // If enabled, check all methods that override a
    // parent method to make sure its signature is
    // compatible with the parent's. This check
    // can add quite a bit of time to the analysis.
    'analyze_signature_compatibility' => true,

    // The minimum severity level to report on. This can be
    // set to Issue::SEVERITY_LOW(0), Issue::SEVERITY_NORMAL(5) or
    // Issue::SEVERITY_CRITICAL(10). Setting it to only
    // critical issues is a good place to start on a big
    // sloppy mature code base.
    'minimum_severity' => 10,

    // Set this to false to emit
    // PhanUndeclaredFunction issues for internal functions
    // that Phan has signatures for,
    // but aren't available in the codebase or the
    // internal functions used to run phan
    'ignore_undeclared_functions_with_known_signatures' => false,

    // If empty, no filter against issues types will be applied.
    // If this white-list is non-empty, only issues within the list
    // will be emitted by Phan.
    'whitelist_issue_types' => [
        //'PhanCompatiblePHP7',  // This only checks for **syntax** where the parsing may have changed. This check is enabled by `backward_compatibility_checks`
        //'PhanDeprecatedFunctionInternal',  // Warns about a few functions deprecated in 7.0 and later.
        //'PhanUndeclaredFunction',  // Check for removed functions such as split() that were deprecated in php 5.x and removed in php 7.0.
    ],
    // Check that 'php --syntax-check' passes for the files being analyzed by Phan.
    'plugins' => ['InvokePHPNativeSyntaxCheckPlugin'],
    // You may wish to add paths both to the older php binary and the newer php binary
    // in 'plugin_config' => ['php_native_syntax_check_binaries' => [...]] to avoid syntax errors
    // while the migration is in progress, unless another tool already does that.

];
