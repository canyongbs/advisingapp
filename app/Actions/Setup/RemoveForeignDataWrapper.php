<?php

namespace App\Actions\Setup;

use Illuminate\Support\Facades\DB;

class RemoveForeignDataWrapper
{
    public function handle(): void
    {
        $database = DB::connection('pgsql');

        $database->statement('DROP EXTENSION IF EXISTS postgres_fdw CASCADE;');
    }
}
