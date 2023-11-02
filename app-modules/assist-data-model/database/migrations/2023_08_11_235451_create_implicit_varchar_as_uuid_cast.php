<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("ALTER TYPE uuid OWNER TO CURRENT_USER;");

        DB::unprepared('DROP CAST IF EXISTS (VARCHAR AS uuid)');

        DB::unprepared('CREATE CAST (VARCHAR AS uuid) WITH INOUT AS IMPLICIT');
    }
};
