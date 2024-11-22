<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        /** Rename service_request_types to case_types*/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_types RENAME TO case_types');

        DB::statement('ALTER TABLE case_types RENAME CONSTRAINT service_request_types_pkey TO case_types_pkey');

        DB::statement('CREATE VIEW service_request_types AS SELECT * FROM case_types');

        DB::commit();

        /** Rename service_request_statuses to case_statuses*/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_statuses RENAME TO case_statuses');

        DB::statement('ALTER TABLE case_statuses RENAME CONSTRAINT service_request_statuses_pkey TO case_statuses_pkey');

        DB::statement('CREATE VIEW service_request_statuses AS SELECT * FROM case_statuses');

        DB::commit();

        /** Rename service_request_priorities to case_priorities*/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_priorities RENAME TO case_priorities');

        DB::statement('ALTER TABLE case_priorities RENAME CONSTRAINT service_request_priorities_pkey TO case_priorities_pkey');

        DB::statement('ALTER TABLE case_priorities RENAME CONSTRAINT service_request_priorities_sla_id_foreign TO case_priorities_sla_id_foreign');

        DB::statement('ALTER TABLE case_priorities RENAME CONSTRAINT service_request_priorities_type_id_foreign TO case_priorities_type_id_foreign');

        DB::statement('CREATE VIEW service_request_priorities AS SELECT * FROM case_priorities');

        DB::commit();
    }

    public function down(): void
    {
        /** Rename case_types to service_request_types*/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS service_request_types');

        DB::statement('ALTER TABLE case_types RENAME TO service_request_types');

        DB::statement('ALTER TABLE service_request_types RENAME CONSTRAINT case_types_pkey TO service_request_types_pkey');

        DB::commit();

        /** Rename case_statuses to service_request_statuses*/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS service_request_statuses');

        DB::statement('ALTER TABLE case_statuses RENAME TO service_request_statuses');

        DB::statement('ALTER TABLE service_request_statuses RENAME CONSTRAINT case_statuses_pkey TO service_request_statuses_pkey');

        DB::commit();

        /** Rename case_priorities to service_request_priorities*/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS service_request_priorities');

        DB::statement('ALTER TABLE case_priorities RENAME TO service_request_priorities');

        DB::statement('ALTER TABLE service_request_priorities RENAME CONSTRAINT case_priorities_pkey TO service_request_priorities_pkey');

        DB::statement('ALTER TABLE service_request_priorities RENAME CONSTRAINT case_priorities_sla_id_foreign TO service_request_priorities_sla_id_foreign');

        DB::statement('ALTER TABLE service_request_priorities RENAME CONSTRAINT case_priorities_type_id_foreign TO service_request_priorities_type_id_foreign');

        DB::commit();
    }
};
