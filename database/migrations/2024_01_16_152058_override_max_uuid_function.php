<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared(<<<SQL
            CREATE OR REPLACE FUNCTION max (uuid, uuid)
            RETURNS uuid AS $$
            BEGIN
                IF $1 IS NULL OR $1 < $2 THEN
                    RETURN $2;
                END IF;

                RETURN $1;
            END;
            $$ LANGUAGE plpgsql;

            CREATE OR REPLACE AGGREGATE max (uuid)
            (
                sfunc = max,
                stype = uuid
            );
        SQL);
    }
};
