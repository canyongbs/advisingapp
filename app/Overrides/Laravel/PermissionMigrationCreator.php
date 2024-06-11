<?php

namespace App\Overrides\Laravel;

use Illuminate\Database\Migrations\MigrationCreator;

class PermissionMigrationCreator extends MigrationCreator
{
    protected function getStub($table, $create)
    {
        return $this->files->get(
            $this->files->exists($customPath = $this->customStubPath . '/permission-migration.stub')
                ? $customPath
                : $this->stubPath() . '/migration.stub'
        );
    }
}
