<?php

use App\Features\CaseManagement;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        /** Rename service_request_types to case_types*/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_types RENAME TO case_types');

        DB::statement('CREATE VIEW service_request_types AS SELECT * FROM case_types');

        DB::commit();

        if (CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS service_request_types');
        }

        /** Rename service_request_statuses to case_statuses*/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_statuses RENAME TO case_statuses');

        DB::statement('CREATE VIEW service_request_statuses AS SELECT * FROM case_statuses');

        DB::commit();

        if (CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS service_request_statuses');
        }

        /** Rename service_request_priorities to case_priorities*/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_priorities RENAME TO case_priorities');

        DB::statement('CREATE VIEW service_request_priorities AS SELECT * FROM case_priorities');

        DB::commit();

        if (CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS service_request_priorities');
        }
    }

    public function down(): void
    {
        /** Rename case_types to service_request_types*/
        DB::beginTransaction();

        DB::statement('ALTER TABLE case_types RENAME TO service_request_types');

        DB::statement('CREATE VIEW case_types AS SELECT * FROM service_request_types');

        DB::commit();

        if (! CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS case_types');
        }

        /** Rename case_statuses to service_request_statuses*/
        DB::beginTransaction();

        DB::statement('ALTER TABLE case_statuses RENAME TO service_request_statuses');

        DB::statement('CREATE VIEW case_statuses AS SELECT * FROM service_request_statuses');

        DB::commit();

        if (! CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS case_statuses');
        }

        /** Rename case_priorities to service_request_priorities*/
        DB::beginTransaction();

        DB::statement('ALTER TABLE case_priorities RENAME TO service_request_priorities');

        DB::statement('CREATE VIEW case_priorities AS SELECT * FROM service_request_priorities');

        DB::commit();

        if (! CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS case_priorities');
        }
    }
};
