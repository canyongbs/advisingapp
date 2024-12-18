<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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

        DB::statement('CREATE VIEW service_request_assignments AS SELECT case_model_id AS service_request_id FROM case_assignments');

        DB::statement('CREATE VIEW service_request_form_authentications AS SELECT case_form_id AS service_request_form_id FROM case_form_authentications');

        DB::statement('CREATE VIEW service_request_form_field_submission AS SELECT case_form_field_id AS service_request_form_field_id, case_form_submission_id AS service_request_form_submission_id FROM case_form_field_submission');

        DB::statement('CREATE VIEW service_request_form_fields AS SELECT case_form_id AS service_request_form_id, case_form_step_id AS service_request_form_step_id FROM case_form_fields');

        DB::statement('CREATE VIEW service_request_form_steps AS SELECT case_form_id AS service_request_form_id FROM case_form_steps');

        DB::statement('CREATE VIEW service_request_form_submissions AS SELECT case_form_id AS service_request_form_id, case_priority_id AS service_request_priority_id FROM case_form_submissions');

        DB::statement('CREATE VIEW service_request_forms AS SELECT case_type_id AS service_request_type_id FROM case_forms');

        DB::statement('CREATE VIEW service_request_histories AS SELECT case_model_id AS service_request_id FROM case_histories');

        DB::statement('CREATE VIEW service_request_updates AS SELECT case_model_id AS service_request_id FROM case_updates');

        DB::statement('CREATE VIEW service_requests AS SELECT case_number AS service_request_number, case_form_submission_id AS service_request_form_submission_id FROM cases');
    }
};
