<?php

namespace Tests\Helpers;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

function rollbackToBefore(string $migrationToRollbackTo)
{
    if (app()->environment('production')) {
        throw new Exception('This cannot safely be run in production environments.');
    }

    $targetMigration = DB::table('migrations')
        ->where('migration', $migrationToRollbackTo)
        ->first();

    if (! $targetMigration) {
        throw new Exception("Migration not found: {$migrationToRollbackTo}");
    }

    $rollbackSteps = DB::table('migrations')
        ->where('id', '>', $targetMigration->id)
        ->count() + 1;

    if ($rollbackSteps > 0) {
        Artisan::call('migrate:rollback', ['--step' => $rollbackSteps]);
    }
}
