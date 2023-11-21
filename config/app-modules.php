<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

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

    'modules_namespace' => 'Assist',

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
        'src/StubClassNamePrefixPlugin.php' => base_path('stubs/app-modules/app/Plugin.php'),
        'src/Models/.gitkeep' => base_path('stubs/app-modules/.gitkeep'),
        'tests/.gitkeep' => base_path('stubs/app-modules/.gitkeep'),
        'database/factories/.gitkeep' => base_path('stubs/app-modules/.gitkeep'),
        'database/migrations/.gitkeep' => base_path('stubs/app-modules/.gitkeep'),
        'database/seeders/.gitkeep' => base_path('stubs/app-modules/.gitkeep'),
        'config/permissions/api/custom.php' => base_path('stubs/app-modules/config/permissions/api/custom.php'),
        'config/permissions/web/custom.php' => base_path('stubs/app-modules/config/permissions/web/custom.php'),
        'config/roles/api/StubModuleName_roles.php' => base_path('stubs/app-modules/config/roles/api/module_roles.php'),
        'config/roles/web/StubModuleName_roles.php' => base_path('stubs/app-modules/config/roles/web/module_roles.php'),
    ],
];
