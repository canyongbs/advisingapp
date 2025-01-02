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

namespace App\Console\Commands;

use App\Overrides\Laravel\PermissionMigrationCreator;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use InterNACHI\Modular\Console\Commands\Make\Modularize;

class CreatePermissionMigration extends MigrateMakeCommand
{
    use Modularize;

    protected $signature = 'make:permission-migration {name : The name of the migration}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
        {--fullpath : Output the full path of the migration (Deprecated)}';

    protected $description = 'Creates a permission migration file.';

    public function __construct(PermissionMigrationCreator $creator, Composer $composer)
    {
        parent::__construct($creator, $composer);
    }

    protected function getMigrationPath()
    {
        $path = parent::getMigrationPath();

        if ($module = $this->module()) {
            $app_directory = $this->laravel->databasePath('migrations');
            $module_directory = $module->path('database/migrations');

            $path = str_replace($app_directory, $module_directory, $path);

            $filesystem = $this->getLaravel()->make(Filesystem::class);

            if (! $filesystem->isDirectory($module_directory)) {
                $filesystem->makeDirectory($module_directory, 0755, true);
            }
        }

        return $path;
    }
}
