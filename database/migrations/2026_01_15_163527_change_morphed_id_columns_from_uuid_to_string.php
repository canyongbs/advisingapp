<?php

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
