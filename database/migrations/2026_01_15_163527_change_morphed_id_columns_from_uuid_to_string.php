<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
        DB::transaction(function () {
            DB::statement('
                ALTER TABLE prospect_phone_numbers
                DROP CONSTRAINT prospect_phone_numbers_prospect_id_foreign
            ');

            DB::statement('
                ALTER TABLE prospect_email_addresses
                DROP CONSTRAINT prospect_email_addresses_prospect_id_foreign
            ');

            DB::statement('
                ALTER TABLE prospect_addresses
                DROP CONSTRAINT prospect_addresses_prospect_id_foreign
            ');

            DB::statement('
                ALTER TABLE prospects
                DROP CONSTRAINT prospects_pkey
            ');

            DB::statement('
                ALTER TABLE prospects
                ALTER COLUMN id TYPE text
                USING id::text
            ');

            DB::statement('
                ALTER TABLE prospect_phone_numbers
                ALTER COLUMN prospect_id TYPE text
                USING prospect_id::text
            ');

            DB::statement('
                ALTER TABLE prospect_email_addresses
                ALTER COLUMN prospect_id TYPE text
                USING prospect_id::text
            ');

            DB::statement('
                ALTER TABLE prospect_addresses
                ALTER COLUMN prospect_id TYPE text
                USING prospect_id::text
            ');

            DB::statement('
                ALTER TABLE prospects
                ADD PRIMARY KEY (id)
            ');

            DB::statement('
                ALTER TABLE prospect_phone_numbers
                ADD CONSTRAINT prospect_phone_numbers_prospect_id_foreign
                FOREIGN KEY (prospect_id)
                REFERENCES prospects(id)
                ON DELETE CASCADE
            ');

            DB::statement('
                ALTER TABLE prospect_email_addresses
                ADD CONSTRAINT prospect_email_addresses_prospect_id_foreign
                FOREIGN KEY (prospect_id)
                REFERENCES prospects(id)
                ON DELETE CASCADE
            ');

            DB::statement('
                ALTER TABLE prospect_addresses
                ADD CONSTRAINT prospect_addresses_prospect_id_foreign
                FOREIGN KEY (prospect_id)
                REFERENCES prospects(id)
                ON DELETE CASCADE
            ');

            DB::statement('
                ALTER TABLE case_feedback
                DROP CONSTRAINT case_feedback_case_id_foreign
            ');

            DB::statement('
                ALTER TABLE case_assignments
                DROP CONSTRAINT case_assignments_case_model_id_foreign
            ');

            DB::statement('
                ALTER TABLE case_histories
                DROP CONSTRAINT case_histories_case_model_id_foreign
            ');

            DB::statement('
                ALTER TABLE case_updates
                DROP CONSTRAINT case_updates_case_model_id_foreign
            ');

            DB::statement('
                ALTER TABLE cases
                DROP CONSTRAINT cases_pkey
            ');

            DB::statement('
                ALTER TABLE cases
                ALTER COLUMN id TYPE text
                USING id::text
            ');

            DB::statement('
                ALTER TABLE case_feedback
                ALTER COLUMN case_id TYPE text
                USING case_id::text
            ');

            DB::statement('
                ALTER TABLE case_assignments
                ALTER COLUMN case_model_id TYPE text
                USING case_model_id::text
            ');

            DB::statement('
                ALTER TABLE case_histories
                ALTER COLUMN case_model_id TYPE text
                USING case_model_id::text
            ');

            DB::statement('
                ALTER TABLE case_updates
                ALTER COLUMN case_model_id TYPE text
                USING case_model_id::text
            ');

            DB::statement('
                ALTER TABLE cases
                ADD PRIMARY KEY (id)
            ');

            DB::statement('
                ALTER TABLE case_feedback
                ADD CONSTRAINT case_feedback_case_id_foreign
                FOREIGN KEY (case_id)
                REFERENCES cases(id)
                ON DELETE CASCADE
            ');

            DB::statement('
                ALTER TABLE case_assignments
                ADD CONSTRAINT case_assignments_case_model_id_foreign
                FOREIGN KEY (case_model_id)
                REFERENCES cases(id)
                ON DELETE CASCADE
            ');

            DB::statement('
                ALTER TABLE case_histories
                ADD CONSTRAINT case_histories_case_model_id_foreign
                FOREIGN KEY (case_model_id)
                REFERENCES cases(id)
                ON DELETE CASCADE
            ');

            DB::statement('
                ALTER TABLE case_updates
                ADD CONSTRAINT case_updates_case_model_id_foreign
                FOREIGN KEY (case_model_id)
                REFERENCES cases(id)
                ON DELETE CASCADE
            ');
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::statement('
                ALTER TABLE case_feedback
                DROP CONSTRAINT case_feedback_case_id_foreign
            ');

            DB::statement('
                ALTER TABLE case_assignments
                DROP CONSTRAINT case_assignments_case_model_id_foreign
            ');

            DB::statement('
                ALTER TABLE case_histories
                DROP CONSTRAINT case_histories_case_model_id_foreign
            ');

            DB::statement('
                ALTER TABLE case_updates
                DROP CONSTRAINT case_updates_case_model_id_foreign
            ');

            DB::statement('
                ALTER TABLE cases
                DROP CONSTRAINT cases_pkey
            ');

            DB::statement('
                ALTER TABLE cases
                ALTER COLUMN id TYPE uuid
                USING id::uuid
            ');

            DB::statement('
                ALTER TABLE case_feedback
                ALTER COLUMN case_id TYPE uuid
                USING case_id::uuid
            ');

            DB::statement('
                ALTER TABLE case_assignments
                ALTER COLUMN case_model_id TYPE uuid
                USING case_model_id::uuid
            ');

            DB::statement('
                ALTER TABLE case_histories
                ALTER COLUMN case_model_id TYPE uuid
                USING case_model_id::uuid
            ');

            DB::statement('
                ALTER TABLE case_updates
                ALTER COLUMN case_model_id TYPE uuid
                USING case_model_id::uuid
            ');

            DB::statement('
                ALTER TABLE cases
                ADD PRIMARY KEY (id)
            ');

            DB::statement('
                ALTER TABLE case_feedback
                ADD CONSTRAINT case_feedback_case_id_foreign
                FOREIGN KEY (case_id)
                REFERENCES cases(id)
                ON DELETE CASCADE
            ');

            DB::statement('
                ALTER TABLE case_assignments
                ADD CONSTRAINT case_assignments_case_model_id_foreign
                FOREIGN KEY (case_model_id)
                REFERENCES cases(id)
                ON DELETE CASCADE
            ');

            DB::statement('
                ALTER TABLE case_histories
                ADD CONSTRAINT case_histories_case_model_id_foreign
                FOREIGN KEY (case_model_id)
                REFERENCES cases(id)
                ON DELETE CASCADE
            ');

            DB::statement('
                ALTER TABLE case_updates
                ADD CONSTRAINT case_updates_case_model_id_foreign
                FOREIGN KEY (case_model_id)
                REFERENCES cases(id)
                ON DELETE CASCADE
            ');

            DB::statement('
                ALTER TABLE prospect_phone_numbers
                DROP CONSTRAINT prospect_phone_numbers_prospect_id_foreign
            ');

            DB::statement('
                ALTER TABLE prospect_email_addresses
                DROP CONSTRAINT prospect_email_addresses_prospect_id_foreign
            ');

            DB::statement('
                ALTER TABLE prospect_addresses
                DROP CONSTRAINT prospect_addresses_prospect_id_foreign
            ');

            DB::statement('
                ALTER TABLE prospects
                DROP CONSTRAINT prospects_pkey
            ');

            DB::statement('
                ALTER TABLE prospects
                ALTER COLUMN id TYPE uuid
                USING id::uuid
            ');

            DB::statement('
                ALTER TABLE prospect_phone_numbers
                ALTER COLUMN prospect_id TYPE uuid
                USING prospect_id::uuid
            ');

            DB::statement('
                ALTER TABLE prospect_email_addresses
                ALTER COLUMN prospect_id TYPE uuid
                USING prospect_id::uuid
            ');

            DB::statement('
                ALTER TABLE prospect_addresses
                ALTER COLUMN prospect_id TYPE uuid
                USING prospect_id::uuid
            ');

            DB::statement('
                ALTER TABLE prospects
                ADD PRIMARY KEY (id)
            ');

            DB::statement('
                ALTER TABLE prospect_phone_numbers
                ADD CONSTRAINT prospect_phone_numbers_prospect_id_foreign
                FOREIGN KEY (prospect_id)
                REFERENCES prospects(id)
                ON DELETE CASCADE
            ');

            DB::statement('
                ALTER TABLE prospect_email_addresses
                ADD CONSTRAINT prospect_email_addresses_prospect_id_foreign
                FOREIGN KEY (prospect_id)
                REFERENCES prospects(id)
                ON DELETE CASCADE
            ');

            DB::statement('
                ALTER TABLE prospect_addresses
                ADD CONSTRAINT prospect_addresses_prospect_id_foreign
                FOREIGN KEY (prospect_id)
                REFERENCES prospects(id)
                ON DELETE CASCADE
            ');
        });
    }
};
