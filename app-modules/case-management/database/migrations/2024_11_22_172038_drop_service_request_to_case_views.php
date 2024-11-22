<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS service_request_types');

        DB::statement('DROP VIEW IF EXISTS service_request_statuses');

        DB::statement('DROP VIEW IF EXISTS service_request_priorities');

        DB::statement('DROP VIEW IF EXISTS service_request_assignments');

        DB::statement('DROP VIEW IF EXISTS service_request_form_authentications');

        DB::statement('DROP VIEW IF EXISTS service_request_form_field_submission');

        DB::statement('DROP VIEW IF EXISTS service_request_form_fields');

        DB::statement('DROP VIEW IF EXISTS service_request_form_steps');

        DB::statement('DROP VIEW IF EXISTS service_request_form_submissions');

        DB::statement('DROP VIEW IF EXISTS service_request_forms');

        DB::statement('DROP VIEW IF EXISTS service_request_histories');

        DB::statement('DROP VIEW IF EXISTS service_request_updates');

        DB::statement('DROP VIEW IF EXISTS service_requests');
    }

    public function down(): void
    {
        DB::statement('CREATE VIEW service_request_types AS SELECT * FROM case_types');

        DB::statement('CREATE VIEW service_request_statuses AS SELECT * FROM case_statuses');

        DB::statement('CREATE VIEW service_request_priorities AS SELECT * FROM case_priorities');

        DB::statement('CREATE VIEW service_request_assignments AS SELECT case_id AS service_request_id FROM case_assignments');

        DB::statement('CREATE VIEW service_request_form_authentications AS SELECT case_form_id AS service_request_form_id FROM case_form_authentications');

        DB::statement('CREATE VIEW service_request_form_field_submission AS SELECT case_form_field_id AS service_request_form_field_id, case_form_submission_id AS service_request_form_submission_id FROM case_form_field_submission');

        DB::statement('CREATE VIEW service_request_form_fields AS SELECT case_form_id AS service_request_form_id, case_form_step_id AS service_request_form_step_id FROM case_form_fields');

        DB::statement('CREATE VIEW service_request_form_steps AS SELECT case_form_id AS service_request_form_id FROM case_form_steps');

        DB::statement('CREATE VIEW service_request_form_submissions AS SELECT case_form_id AS service_request_form_id, case_priority_id AS service_request_priority_id FROM case_form_submissions');

        DB::statement('CREATE VIEW service_request_forms AS SELECT case_type_id AS service_request_type_id FROM case_forms');

        DB::statement('CREATE VIEW service_request_histories AS SELECT case_id AS service_request_id FROM case_histories');

        DB::statement('CREATE VIEW service_request_updates AS SELECT case_id AS service_request_id FROM case_updates');

        DB::statement('CREATE VIEW service_requests AS SELECT case_number AS service_request_number, case_form_submission_id AS service_request_form_submission_id FROM cases');
    }
};
