<?php

namespace App\Console\Commands;

use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
use App\Overrides\Laravel\PermissionMigrationCreator;
use InterNACHI\Modular\Console\Commands\Make\Modularize;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;

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
