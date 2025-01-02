<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

return [
    /*
    |--------------------------------------------------------------------------
    | Modules Namespace
    |--------------------------------------------------------------------------
    |
    | This is the PHP namespace that your modules will be created in. For
    | example, a module called "Helpers" will be placed in \Modules\Helpers
    | by default.
    |
    | It is *highly recommended* that you configure this to your organization
    | name to make extracting modules to their own package easier (should you
    | choose to ever do so).
    |
    | If you set the namespace, you should also set the vendor name to match.
    |
    */

    'modules_namespace' => 'AdvisingApp',

    /*
    |--------------------------------------------------------------------------
    | Composer "Vendor" Name
    |--------------------------------------------------------------------------
    |
    | This is the prefix used for your composer.json file. This should be the
    | kebab-case version of your module namespace (if left null, we will
    | generate the kebab-case version for you).
    |
    */

    'modules_vendor' => 'canyon-gbs',

    /*
    |--------------------------------------------------------------------------
    | Modules Directory
    |--------------------------------------------------------------------------
    |
    | If you want to install modules in a custom directory, you can do so here.
    | Keeping the default `app-modules/` directory is highly recommended,
    | though, as it keeps your modules near the rest of your application code
    | in an alpha-sorted directory listing.
    |
    */

    'modules_directory' => 'app-modules',

    /*
    |--------------------------------------------------------------------------
    | Base Test Case
    |--------------------------------------------------------------------------
    |
    | This is the base TestCase class name that auto-generated Tests should
    | extend. By default it assumes the default \Tests\TestCase exists.
    |
    */

    'tests_base' => 'Tests\TestCase',

    /*
    |--------------------------------------------------------------------------
    | Custom Stubs
    |--------------------------------------------------------------------------
    |
    | If you would like to use your own custom stubs for new modules, you can
    | configure those here. This should be an array where the key is the path
    | relative to the module and the value is the absolute path to the stub
    | stub file. Destination paths and contents support placeholders. See the
    | README.md file for more information.
    |
    | For example:
    |
    | 'stubs' => [
    | 	'src/Providers/StubClassNamePrefixServiceProvider.php' => base_path('stubs/app-modules/ServiceProvider.php'),
    | ],
    */

    'stubs' => [
        'composer.json' => base_path('stubs/app-modules/composer-stub.json'),
        'src/Providers/StubClassNamePrefixServiceProvider.php' => base_path('stubs/app-modules/app/ServiceProvider.php'),
        'src/Registries/StubClassNamePrefixRbacRegistry.php' => base_path('stubs/app-modules/app/Registries/RbacRegistry.php'),
        'src/StubClassNamePrefixPlugin.php' => base_path('stubs/app-modules/app/Plugin.php'),
        'src/Models/.gitkeep' => base_path('stubs/app-modules/.gitkeep'),
        'tests/.gitkeep' => base_path('stubs/app-modules/.gitkeep'),
        'database/factories/.gitkeep' => base_path('stubs/app-modules/.gitkeep'),
        'database/migrations/.gitkeep' => base_path('stubs/app-modules/.gitkeep'),
        'database/seeders/.gitkeep' => base_path('stubs/app-modules/.gitkeep'),
        'config/roles/api/StubModuleName_roles.php' => base_path('stubs/app-modules/config/roles/api/module_roles.php'),
        'config/roles/web/StubModuleName_roles.php' => base_path('stubs/app-modules/config/roles/web/module_roles.php'),
    ],
];
