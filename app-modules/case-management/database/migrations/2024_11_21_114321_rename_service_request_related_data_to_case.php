<?php

use App\Features\CaseManagement;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        /**
         * Renaming the table service_request_assignments to case_assignments
         * Renaming the column service_request_id to case_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_assignments RENAME TO case_assignments');

        DB::statement('ALTER TABLE case_assignments RENAME COLUMN service_request_id TO case_id');

        DB::statement('CREATE VIEW service_request_assignments AS SELECT case_id AS service_request_id FROM case_assignments');

        DB::commit();

        /** Drop view */
        if (CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS service_request_assignments');
        }

        /**
         * Renaming the table service_request_form_authentications to case_form_authentications
         * Renaming the column service_request_form_id to case_form_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_form_authentications RENAME TO case_form_authentications');

        DB::statement('ALTER TABLE case_form_authentications RENAME COLUMN service_request_form_id TO case_form_id');

        DB::statement('CREATE VIEW service_request_form_authentications AS SELECT case_form_id AS service_request_form_id FROM case_form_authentications');

        DB::commit();

        /** Drop view */
        if (CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS service_request_form_authentications');
        }

        /**
         * Renaming the table service_request_form_field_submission to case_form_field_submission
         * Renaming the column service_request_form_field_id to case_form_field_id and service_request_form_submission_id to case_form_submission_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_form_field_submission RENAME TO case_form_field_submission');

        DB::statement('ALTER TABLE case_form_field_submission RENAME COLUMN service_request_form_field_id TO case_form_field_id');
        DB::statement('ALTER TABLE case_form_field_submission RENAME COLUMN service_request_form_submission_id TO case_form_submission_id');

        DB::statement('CREATE VIEW service_request_form_field_submission AS SELECT case_form_field_id AS service_request_form_field_id, case_form_submission_id AS service_request_form_submission_id FROM case_form_field_submission');

        DB::commit();

        /** Drop view */
        if (CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS service_request_form_field_submission');
        }

        /**
         * Renaming the table service_request_form_fields to case_form_fields
         * Renaming the column service_request_form_id to case_form_id and service_request_form_step_id to case_form_step_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_form_fields RENAME TO case_form_fields');

        DB::statement('ALTER TABLE case_form_fields RENAME COLUMN service_request_form_id TO case_form_id');
        DB::statement('ALTER TABLE case_form_fields RENAME COLUMN service_request_form_step_id TO case_form_step_id');

        DB::statement('CREATE VIEW service_request_form_fields AS SELECT case_form_id AS service_request_form_id, case_form_step_id AS service_request_form_step_id FROM case_form_fields');

        DB::commit();

        /** Drop view */
        if (CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS service_request_form_fields');
        }

        /**
         * Renaming the table service_request_form_steps to case_form_steps
         * Renaming the column service_request_form_id to case_form_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_form_steps RENAME TO case_form_steps');

        DB::statement('ALTER TABLE case_form_steps RENAME COLUMN service_request_form_id TO case_form_id');

        DB::statement('CREATE VIEW service_request_form_steps AS SELECT case_form_id AS service_request_form_id FROM case_form_steps');

        DB::commit();

        /** Drop view */
        if (CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS service_request_form_steps');
        }

        /**
         * Renaming the table service_request_form_submissions to case_form_submissions
         * Renaming the column service_request_form_id to case_form_id and service_request_priority_id to case_priority_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_form_submissions RENAME TO case_form_submissions');

        DB::statement('ALTER TABLE case_form_submissions RENAME COLUMN service_request_form_id TO case_form_id');
        DB::statement('ALTER TABLE case_form_submissions RENAME COLUMN service_request_priority_id TO case_priority_id');

        DB::statement('CREATE VIEW service_request_form_submissions AS SELECT case_form_id AS service_request_form_id, case_priority_id AS service_request_priority_id FROM case_form_submissions');

        DB::commit();

        /** Drop view */
        if (CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS service_request_form_submissions');
        }

        /**
         * Renaming the table service_request_forms to case_forms
         * Renaming the column service_request_type_id to case_type_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_forms RENAME TO case_forms');

        DB::statement('ALTER TABLE case_forms RENAME COLUMN service_request_type_id TO case_type_id');

        DB::statement('CREATE VIEW service_request_forms AS SELECT case_type_id AS service_request_type_id FROM case_forms');

        DB::commit();

        /** Drop view */
        if (CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS service_request_forms');
        }

        /**
         * Renaming the table service_request_histories to case_histories
         * Renaming the column service_request_id to case_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_histories RENAME TO case_histories');

        DB::statement('ALTER TABLE case_histories RENAME COLUMN service_request_id TO case_id');

        DB::statement('CREATE VIEW service_request_histories AS SELECT case_id AS service_request_id FROM case_histories');

        DB::commit();

        /** Drop view */
        if (CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS service_request_histories');
        }

        /**
         * Renaming the table service_request_updates to case_updates
         * Renaming the column service_request_id to case_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_updates RENAME TO case_updates');

        DB::statement('ALTER TABLE case_updates RENAME COLUMN service_request_id TO case_id');

        DB::statement('CREATE VIEW service_request_updates AS SELECT case_id AS service_request_id FROM case_updates');

        DB::commit();

        /** Drop view */
        if (CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS service_request_updates');
        }

        /**
         * Renaming the table service_requests to cases
         * Renaming the column service_request_number to case_number and service_request_form_submission_id to case_form_submission_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_requests RENAME TO cases');

        DB::statement('ALTER TABLE cases RENAME COLUMN service_request_number TO case_number');
        DB::statement('ALTER TABLE cases RENAME COLUMN service_request_form_submission_id TO case_form_submission_id');

        DB::statement('CREATE VIEW service_requests AS SELECT case_number AS service_request_number, case_form_submission_id AS service_request_form_submission_id FROM cases');

        DB::commit();

        /** Drop view */
        if (CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS service_requests');
        }
    }

    public function down(): void
    {
        /**
         * Renaming the table case_assignments to service_request_assignments
         * Renaming the column case_id to service_request_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE case_assignments RENAME TO service_request_assignments');

        DB::statement('ALTER TABLE service_request_assignments RENAME COLUMN case_id TO service_request_id');

        DB::statement('CREATE VIEW case_assignments AS SELECT service_request_id AS case_id FROM service_request_assignments');

        DB::commit();

        /** Drop view */
        if (! CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS case_assignments');
        }

        /**
         * Renaming the table case_form_authentications to service_request_form_authentications
         * Renaming the column case_form_id to service_request_form_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE case_form_authentications RENAME TO service_request_form_authentications');

        DB::statement('ALTER TABLE service_request_form_authentications RENAME COLUMN case_form_id TO service_request_form_id');

        DB::statement('CREATE VIEW case_form_authentications AS SELECT service_request_form_id AS case_form_id FROM service_request_form_authentications');

        DB::commit();

        /** Drop view */
        if (! CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS case_form_authentications');
        }

        /**
         * Renaming the table case_form_field_submission to service_request_form_field_submission
         * Renaming the column case_form_field_id to service_request_form_field_id and case_form_submission_id to service_request_form_submission_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE case_form_field_submission RENAME TO service_request_form_field_submission');

        DB::statement('ALTER TABLE service_request_form_field_submission RENAME COLUMN case_form_field_id TO service_request_form_field_id');
        DB::statement('ALTER TABLE service_request_form_field_submission RENAME COLUMN case_form_submission_id TO service_request_form_submission_id');

        DB::statement('CREATE VIEW case_form_field_submission AS SELECT service_request_form_field_id AS case_form_field_id, service_request_form_submission_id AS case_form_submission_id FROM service_request_form_field_submission');

        DB::commit();

        /** Drop view */
        if (! CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS case_form_field_submission');
        }

        /**
         * Renaming the table case_form_fields to service_request_form_fields
         * Renaming the column case_form_id to service_request_form_id and case_form_step_id to service_request_form_step_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE case_form_fields RENAME TO service_request_form_fields');

        DB::statement('ALTER TABLE service_request_form_fields RENAME COLUMN case_form_id TO service_request_form_id');
        DB::statement('ALTER TABLE service_request_form_fields RENAME COLUMN case_form_step_id TO service_request_form_step_id');

        DB::statement('CREATE VIEW case_form_fields AS SELECT service_request_form_id AS case_form_id, service_request_form_step_id AS case_form_step_id FROM service_request_form_fields');

        DB::commit();

        /** Drop view */
        if (! CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS case_form_fields');
        }

        /**
         * Renaming the table case_form_steps to service_request_form_steps
         * Renaming the column case_form_id to service_request_form_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE case_form_steps RENAME TO service_request_form_steps');

        DB::statement('ALTER TABLE service_request_form_steps RENAME COLUMN case_form_id TO service_request_form_id');

        DB::statement('CREATE VIEW case_form_steps AS SELECT service_request_form_id AS case_form_id FROM service_request_form_steps');

        DB::commit();

        /** Drop view */
        if (! CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS case_form_steps');
        }

        /**
        * Renaming the table case_form_submissions to service_request_form_submissions
        * Renaming the column case_form_id to service_request_form_id and case_priority_id to service_request_priority_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE case_form_submissions RENAME TO service_request_form_submissions');

        DB::statement('ALTER TABLE service_request_form_submissions RENAME COLUMN case_form_id TO service_request_form_id');
        DB::statement('ALTER TABLE service_request_form_submissions RENAME COLUMN case_priority_id TO service_request_priority_id');

        DB::statement('CREATE VIEW case_form_submissions AS SELECT service_request_form_id AS case_form_id, service_request_priority_id AS case_priority_id FROM service_request_form_submissions');

        DB::commit();

        /** Drop view */
        if (! CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS case_form_submissions');
        }

        /**
         * Renaming the table case_forms to service_request_forms
         * Renaming the column case_type_id to service_request_type_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE case_forms RENAME TO service_request_forms');

        DB::statement('ALTER TABLE service_request_forms RENAME COLUMN case_type_id TO service_request_type_id');

        DB::statement('CREATE VIEW case_forms AS SELECT service_request_type_id AS case_type_id FROM service_request_forms');

        DB::commit();

        /** Drop view */
        if (! CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS case_forms');
        }

        /**
         * Renaming the table case_histories to service_request_histories
         * Renaming the column case_id to service_request_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE case_histories RENAME TO service_request_histories');

        DB::statement('ALTER TABLE service_request_histories RENAME COLUMN case_id TO service_request_id');

        DB::statement('CREATE VIEW case_histories AS SELECT service_request_id AS case_id FROM service_request_histories');

        DB::commit();

        /** Drop view */
        if (! CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS case_histories');
        }

        /**
         * Renaming the table case_updates to service_request_updates
         * Renaming the column case_id to service_request_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE case_updates RENAME TO service_request_updates');

        DB::statement('ALTER TABLE service_request_updates RENAME COLUMN case_id TO service_request_id');

        DB::statement('CREATE VIEW case_updates AS SELECT service_request_id AS case_id FROM service_request_updates');

        DB::commit();

        /** Drop view */
        if (! CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS case_updates');
        }

        /**
         * Renaming the table cases to service_requests
         * Renaming the column case_number to service_request_number and case_form_submission_id to service_request_form_submission_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE cases RENAME TO service_requests');

        DB::statement('ALTER TABLE service_requests RENAME COLUMN case_number TO service_request_number');
        DB::statement('ALTER TABLE service_requests RENAME COLUMN case_form_submission_id TO service_request_form_submission_id');

        DB::statement('CREATE VIEW cases AS SELECT service_request_number AS case_number, service_request_form_submission_id AS case_form_submission_id FROM service_requests');

        DB::commit();

        /** Drop view */
        if (! CaseManagement::active()) {
            DB::statement('DROP VIEW IF EXISTS cases');
        }
    }
};
