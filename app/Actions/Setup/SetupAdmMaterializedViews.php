<?php

namespace App\Actions\Setup;

use Illuminate\Support\Facades\DB;

class SetupAdmMaterializedViews
{
    public function handle(string $connection, string $remoteTable, ?string $indexColumn = null): void
    {
        $database = DB::connection($connection);

        $localTable = $remoteTable . '_local';

        $database->statement("DROP MATERIALIZED VIEW IF EXISTS {$localTable} CASCADE;");

        $database->statement("CREATE MATERIALIZED VIEW {$localTable} AS SELECT * FROM {$remoteTable};");

        if ($indexColumn) {
            $database->statement("CREATE INDEX idx_{$remoteTable}_{$indexColumn} ON {$localTable} ({$indexColumn});");
        }
    }
}
