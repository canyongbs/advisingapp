<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        /**
         * Renaming the table service_request_assignments to case_assignments
         * Renaming the column service_request_id to case_model_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_assignments RENAME TO case_assignments');

        DB::statement('ALTER TABLE case_assignments RENAME CONSTRAINT service_request_assignments_pkey TO case_assignments_pkey');

        DB::statement('ALTER TABLE case_assignments RENAME CONSTRAINT service_request_assignments_assigned_by_id_foreign TO case_assignments_assigned_by_id_foreign');

        DB::statement('ALTER TABLE case_assignments RENAME CONSTRAINT service_request_assignments_service_request_id_foreign TO case_assignments_case_model_id_foreign');

        DB::statement('ALTER TABLE case_assignments RENAME CONSTRAINT service_request_assignments_user_id_foreign TO case_assignments_user_id_foreign');

        DB::statement('ALTER TABLE case_assignments RENAME COLUMN service_request_id TO case_model_id');

        DB::statement('CREATE VIEW service_request_assignments AS SELECT case_model_id AS service_request_id FROM case_assignments');

        DB::commit();

        /**
         * Renaming the table service_request_form_authentications to case_form_authentications
         * Renaming the column service_request_form_id to case_form_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_form_authentications RENAME TO case_form_authentications');

        DB::statement('ALTER TABLE case_form_authentications RENAME CONSTRAINT service_request_form_authentications_pkey TO case_form_authentications_pkey');

        // DB::statement('ALTER TABLE case_form_authentications RENAME INDEX service_request_form_authentications_author_type_author_id_inde TO case_form_authentications_author_type_author_id_inde');

        DB::statement('ALTER INDEX service_request_form_authentications_author_type_author_id_inde RENAME TO case_form_authentications_author_type_author_id_inde');

        DB::statement('ALTER TABLE case_form_authentications RENAME CONSTRAINT service_request_form_authentications_service_request_form_id_fo TO case_form_authentications_case_form_id_fo');

        DB::statement('ALTER TABLE case_form_authentications RENAME COLUMN service_request_form_id TO case_form_id');

        DB::statement('CREATE VIEW service_request_form_authentications AS SELECT case_form_id AS service_request_form_id FROM case_form_authentications');

        DB::commit();

        /**
         * Renaming the table service_request_form_field_submission to case_form_field_submission
         * Renaming the column service_request_form_field_id to case_form_field_id and service_request_form_submission_id to case_form_submission_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_form_field_submission RENAME TO case_form_field_submission');

        DB::statement('ALTER TABLE case_form_field_submission RENAME CONSTRAINT service_request_form_field_submission_pkey TO case_form_field_submission_pkey');

        DB::statement('ALTER TABLE case_form_field_submission RENAME CONSTRAINT service_request_form_field_submission_service_request_form_fiel TO case_form_field_submission_case_form_fiel');

        DB::statement('ALTER TABLE case_form_field_submission RENAME CONSTRAINT service_request_form_field_submission_service_request_form_subm TO case_form_field_submission_case_form_subm');

        DB::statement('ALTER TABLE case_form_field_submission RENAME COLUMN service_request_form_field_id TO case_form_field_id');

        DB::statement('ALTER TABLE case_form_field_submission RENAME COLUMN service_request_form_submission_id TO case_form_submission_id');

        DB::statement('CREATE VIEW service_request_form_field_submission AS SELECT case_form_field_id AS service_request_form_field_id, case_form_submission_id AS service_request_form_submission_id FROM case_form_field_submission');

        DB::commit();

        /**
         * Renaming the table service_request_form_fields to case_form_fields
         * Renaming the column service_request_form_id to case_form_id and service_request_form_step_id to case_form_step_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_form_fields RENAME TO case_form_fields');

        DB::statement('ALTER TABLE case_form_fields RENAME CONSTRAINT service_request_form_fields_pkey TO case_form_fields_pkey');

        DB::statement('ALTER TABLE case_form_fields RENAME CONSTRAINT service_request_form_fields_service_request_form_id_foreign TO case_form_fields_case_form_id_foreign');

        DB::statement('ALTER TABLE case_form_fields RENAME CONSTRAINT service_request_form_fields_service_request_form_step_id_foreig TO case_form_fields_case_form_step_id_foreig');

        DB::statement('ALTER TABLE case_form_fields RENAME COLUMN service_request_form_id TO case_form_id');

        DB::statement('ALTER TABLE case_form_fields RENAME COLUMN service_request_form_step_id TO case_form_step_id');

        DB::statement('CREATE VIEW service_request_form_fields AS SELECT case_form_id AS service_request_form_id, case_form_step_id AS service_request_form_step_id FROM case_form_fields');

        DB::commit();

        /**
         * Renaming the table service_request_form_steps to case_form_steps
         * Renaming the column service_request_form_id to case_form_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_form_steps RENAME TO case_form_steps');

        DB::statement('ALTER TABLE case_form_steps RENAME CONSTRAINT service_request_form_steps_pkey TO case_form_steps_pkey');

        DB::statement('ALTER TABLE case_form_steps RENAME CONSTRAINT service_request_form_steps_service_request_form_id_foreign TO case_form_steps_case_form_id_foreign');

        DB::statement('ALTER TABLE case_form_steps RENAME COLUMN service_request_form_id TO case_form_id');

        DB::statement('CREATE VIEW service_request_form_steps AS SELECT case_form_id AS service_request_form_id FROM case_form_steps');

        DB::commit();

        /**
         * Renaming the table service_request_form_submissions to case_form_submissions
         * Renaming the column service_request_form_id to case_form_id and service_request_priority_id to case_priority_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_form_submissions RENAME TO case_form_submissions');

        DB::statement('ALTER TABLE case_form_submissions RENAME CONSTRAINT service_request_form_submissions_pkey TO case_form_submissions_pkey');

        // DB::statement('ALTER TABLE case_form_submissions RENAME INDEX service_request_form_submissions_author_type_author_id_index TO case_form_submissions_author_type_author_id_index');

        DB::statement('ALTER INDEX service_request_form_submissions_author_type_author_id_index RENAME TO case_form_submissions_author_type_author_id_index');

        DB::statement('ALTER TABLE case_form_submissions RENAME CONSTRAINT service_request_form_submissions_requester_id_foreign TO case_form_submissions_requester_id_foreign');

        DB::statement('ALTER TABLE case_form_submissions RENAME CONSTRAINT service_request_form_submissions_service_request_form_id_foreig TO case_form_submissions_case_form_id_foreig');

        DB::statement('ALTER TABLE case_form_submissions RENAME CONSTRAINT service_request_form_submissions_service_request_priority_id_fo TO case_form_submissions_case_priority_id_fo');

        DB::statement('ALTER TABLE case_form_submissions RENAME COLUMN service_request_form_id TO case_form_id');

        DB::statement('ALTER TABLE case_form_submissions RENAME COLUMN service_request_priority_id TO case_priority_id');

        DB::statement('CREATE VIEW service_request_form_submissions AS SELECT case_form_id AS service_request_form_id, case_priority_id AS service_request_priority_id FROM case_form_submissions');

        DB::commit();

        /**
         * Renaming the table service_request_forms to case_forms
         * Renaming the column service_request_type_id to case_type_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_forms RENAME TO case_forms');

        DB::statement('ALTER TABLE case_forms RENAME CONSTRAINT service_request_forms_pkey TO case_forms_pkey');

        DB::statement('ALTER TABLE case_forms RENAME CONSTRAINT service_request_forms_name_unique TO case_forms_name_unique');

        DB::statement('ALTER TABLE case_forms RENAME CONSTRAINT service_request_forms_service_request_type_id_foreign TO case_forms_case_type_id_foreign');

        DB::statement('ALTER TABLE case_forms RENAME COLUMN service_request_type_id TO case_type_id');

        DB::statement('CREATE VIEW service_request_forms AS SELECT case_type_id AS service_request_type_id FROM case_forms');

        DB::commit();

        /**
         * Renaming the table service_request_histories to case_histories
         * Renaming the column service_request_id to case_model_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_histories RENAME TO case_histories');

        DB::statement('ALTER TABLE case_histories RENAME CONSTRAINT service_request_histories_pkey TO case_histories_pkey');

        DB::statement('ALTER TABLE case_histories RENAME CONSTRAINT service_request_histories_service_request_id_foreign TO case_histories_case_model_id_foreign');

        DB::statement('ALTER TABLE case_histories RENAME COLUMN service_request_id TO case_model_id');

        DB::statement('CREATE VIEW service_request_histories AS SELECT case_model_id AS service_request_id FROM case_histories');

        DB::commit();

        /**
         * Renaming the table service_request_updates to case_updates
         * Renaming the column service_request_id to case_model_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_request_updates RENAME TO case_updates');

        DB::statement('ALTER TABLE case_updates RENAME CONSTRAINT service_request_updates_pkey TO case_updates_pkey');

        DB::statement('ALTER TABLE case_updates RENAME CONSTRAINT service_request_updates_service_request_id_foreign TO case_updates_case_model_id_foreign');

        DB::statement('ALTER TABLE case_updates RENAME COLUMN service_request_id TO case_model_id');

        DB::statement('CREATE VIEW service_request_updates AS SELECT case_model_id AS service_request_id FROM case_updates');

        DB::commit();

        /**
         * Renaming the table service_requests to cases
         * Renaming the column service_request_number to case_number and service_request_form_submission_id to case_form_submission_id in table
        **/
        DB::beginTransaction();

        DB::statement('ALTER TABLE service_requests RENAME TO cases');

        DB::statement('ALTER TABLE cases RENAME CONSTRAINT service_requests_pkey TO cases_pkey');

        DB::statement('ALTER TABLE cases RENAME CONSTRAINT service_requests_service_request_number_unique TO cases_case_number_unique');

        DB::statement('ALTER TABLE cases RENAME CONSTRAINT service_requests_created_by_id_foreign TO cases_created_by_id_foreign');

        DB::statement('ALTER TABLE cases RENAME CONSTRAINT service_requests_division_id_foreign TO cases_division_id_foreign');

        DB::statement('ALTER TABLE cases RENAME CONSTRAINT service_requests_priority_id_foreign TO cases_priority_id_foreign');

        DB::statement('ALTER TABLE cases RENAME CONSTRAINT service_requests_service_request_form_submission_id_foreign TO cases_case_form_submission_id_foreign');

        DB::statement('ALTER TABLE cases RENAME CONSTRAINT service_requests_status_id_foreign TO cases_status_id_foreign');

        DB::statement('ALTER TABLE cases RENAME COLUMN service_request_number TO case_number');

        DB::statement('ALTER TABLE cases RENAME COLUMN service_request_form_submission_id TO case_form_submission_id');

        DB::statement('CREATE VIEW service_requests AS SELECT case_number AS service_request_number, case_form_submission_id AS service_request_form_submission_id FROM cases');

        DB::commit();
    }

    public function down(): void
    {
        /**
         * Renaming the table case_assignments to service_request_assignments
         * Renaming the column case_model_id to service_request_id in table
        **/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS service_request_assignments');

        DB::statement('ALTER TABLE case_assignments RENAME TO service_request_assignments');

        DB::statement('ALTER TABLE service_request_assignments RENAME COLUMN case_model_id TO service_request_id');

        DB::statement('ALTER TABLE service_request_assignments RENAME CONSTRAINT case_assignments_pkey TO service_request_assignments_pkey');

        DB::statement('ALTER TABLE service_request_assignments RENAME CONSTRAINT case_assignments_assigned_by_id_foreign TO service_request_assignments_assigned_by_id_foreign');

        DB::statement('ALTER TABLE service_request_assignments RENAME CONSTRAINT case_assignments_case_model_id_foreign TO service_request_assignments_service_request_id_foreign');

        DB::statement('ALTER TABLE service_request_assignments RENAME CONSTRAINT case_assignments_user_id_foreign TO service_request_assignments_user_id_foreign');

        DB::commit();

        /**
         * Renaming the table case_form_authentications to service_request_form_authentications
         * Renaming the column case_form_id to service_request_form_id in table
        **/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS service_request_form_authentications');

        DB::statement('ALTER TABLE case_form_authentications RENAME TO service_request_form_authentications');

        DB::statement('ALTER TABLE service_request_form_authentications RENAME COLUMN case_form_id TO service_request_form_id');

        DB::statement('ALTER TABLE service_request_form_authentications RENAME CONSTRAINT case_form_authentications_pkey TO service_request_form_authentications_pkey');

        // DB::statement('ALTER TABLE service_request_form_authentications RENAME INDEX case_form_authentications_author_type_author_id_inde TO service_request_form_authentications_author_type_author_id_inde');

        DB::statement('ALTER INDEX case_form_authentications_author_type_author_id_inde RENAME TO service_request_form_authentications_author_type_author_id_inde');

        DB::statement('ALTER TABLE service_request_form_authentications RENAME CONSTRAINT case_form_authentications_case_form_id_fo TO service_request_form_authentications_service_request_form_id_fo');

        DB::commit();

        /**
         * Renaming the table case_form_field_submission to service_request_form_field_submission
         * Renaming the column case_form_field_id to service_request_form_field_id and case_form_submission_id to service_request_form_submission_id in table
        **/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS service_request_form_field_submission');

        DB::statement('ALTER TABLE case_form_field_submission RENAME TO service_request_form_field_submission');

        DB::statement('ALTER TABLE service_request_form_field_submission RENAME COLUMN case_form_field_id TO service_request_form_field_id');

        DB::statement('ALTER TABLE service_request_form_field_submission RENAME COLUMN case_form_submission_id TO service_request_form_submission_id');

        DB::statement('ALTER TABLE service_request_form_field_submission RENAME CONSTRAINT case_form_field_submission_pkey TO service_request_form_field_submission_pkey');

        DB::statement('ALTER TABLE service_request_form_field_submission RENAME CONSTRAINT case_form_field_submission_case_form_fiel TO service_request_form_field_submission_service_request_form_fiel');

        DB::statement('ALTER TABLE service_request_form_field_submission RENAME CONSTRAINT case_form_field_submission_case_form_subm TO service_request_form_field_submission_service_request_form_subm');

        DB::commit();

        /**
         * Renaming the table case_form_fields to service_request_form_fields
         * Renaming the column case_form_id to service_request_form_id and case_form_step_id to service_request_form_step_id in table
        **/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS service_request_form_fields');

        DB::statement('ALTER TABLE case_form_fields RENAME TO service_request_form_fields');

        DB::statement('ALTER TABLE service_request_form_fields RENAME COLUMN case_form_id TO service_request_form_id');

        DB::statement('ALTER TABLE service_request_form_fields RENAME COLUMN case_form_step_id TO service_request_form_step_id');

        DB::statement('ALTER TABLE service_request_form_fields RENAME CONSTRAINT case_form_fields_pkey TO service_request_form_fields_pkey');

        DB::statement('ALTER TABLE service_request_form_fields RENAME CONSTRAINT case_form_fields_case_form_id_foreign TO service_request_form_fields_service_request_form_id_foreign');

        DB::statement('ALTER TABLE service_request_form_fields RENAME CONSTRAINT case_form_fields_case_form_step_id_foreig TO service_request_form_fields_service_request_form_step_id_foreig');

        DB::commit();

        /**
         * Renaming the table case_form_steps to service_request_form_steps
         * Renaming the column case_form_id to service_request_form_id in table
        **/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS service_request_form_steps');

        DB::statement('ALTER TABLE case_form_steps RENAME TO service_request_form_steps');

        DB::statement('ALTER TABLE service_request_form_steps RENAME COLUMN case_form_id TO service_request_form_id');

        DB::statement('ALTER TABLE service_request_form_steps RENAME CONSTRAINT case_form_steps_pkey TO service_request_form_steps_pkey');

        DB::statement('ALTER TABLE service_request_form_steps RENAME CONSTRAINT case_form_steps_case_form_id_foreign TO service_request_form_steps_service_request_form_id_foreign');

        DB::commit();

        /**
        * Renaming the table case_form_submissions to service_request_form_submissions
        * Renaming the column case_form_id to service_request_form_id and case_priority_id to service_request_priority_id in table
        **/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS service_request_form_submissions');

        DB::statement('ALTER TABLE case_form_submissions RENAME TO service_request_form_submissions');

        DB::statement('ALTER TABLE service_request_form_submissions RENAME COLUMN case_form_id TO service_request_form_id');

        DB::statement('ALTER TABLE service_request_form_submissions RENAME COLUMN case_priority_id TO service_request_priority_id');

        DB::statement('ALTER TABLE service_request_form_submissions RENAME CONSTRAINT case_form_submissions_pkey TO service_request_form_submissions_pkey');

        // DB::statement('ALTER TABLE service_request_form_submissions RENAME INDEX case_form_submissions_author_type_author_id_index TO service_request_form_submissions_author_type_author_id_index');

        DB::statement('ALTER INDEX case_form_submissions_author_type_author_id_index RENAME TO service_request_form_submissions_author_type_author_id_index');

        DB::statement('ALTER TABLE service_request_form_submissions RENAME CONSTRAINT case_form_submissions_requester_id_foreign TO service_request_form_submissions_requester_id_foreign');

        DB::statement('ALTER TABLE service_request_form_submissions RENAME CONSTRAINT case_form_submissions_case_form_id_foreig TO service_request_form_submissions_service_request_form_id_foreig');

        DB::statement('ALTER TABLE service_request_form_submissions RENAME CONSTRAINT case_form_submissions_case_priority_id_fo TO service_request_form_submissions_service_request_priority_id_fo');

        DB::commit();

        /**
         * Renaming the table case_forms to service_request_forms
         * Renaming the column case_type_id to service_request_type_id in table
        **/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS service_request_forms');

        DB::statement('ALTER TABLE case_forms RENAME TO service_request_forms');

        DB::statement('ALTER TABLE service_request_forms RENAME COLUMN case_type_id TO service_request_type_id');

        DB::statement('ALTER TABLE service_request_forms RENAME CONSTRAINT case_forms_pkey TO service_request_forms_pkey');

        DB::statement('ALTER TABLE service_request_forms RENAME CONSTRAINT case_forms_name_unique TO service_request_forms_name_unique');

        DB::statement('ALTER TABLE service_request_forms RENAME CONSTRAINT case_forms_case_type_id_foreign TO service_request_forms_service_request_type_id_foreign');

        DB::commit();

        /**
         * Renaming the table case_histories to service_request_histories
         * Renaming the column case_model_id to service_request_id in table
        **/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS service_request_histories');

        DB::statement('ALTER TABLE case_histories RENAME TO service_request_histories');

        DB::statement('ALTER TABLE service_request_histories RENAME COLUMN case_model_id TO service_request_id');

        DB::statement('ALTER TABLE service_request_histories RENAME CONSTRAINT case_histories_pkey TO service_request_histories_pkey');

        DB::statement('ALTER TABLE service_request_histories RENAME CONSTRAINT case_histories_case_model_id_foreign TO service_request_histories_service_request_id_foreign');

        DB::commit();

        /**
         * Renaming the table case_updates to service_request_updates
         * Renaming the column case_model_id to service_request_id in table
        **/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS service_request_updates');

        DB::statement('ALTER TABLE case_updates RENAME TO service_request_updates');

        DB::statement('ALTER TABLE service_request_updates RENAME COLUMN case_model_id TO service_request_id');

        DB::statement('ALTER TABLE service_request_updates RENAME CONSTRAINT case_updates_pkey TO service_request_updates_pkey');

        DB::statement('ALTER TABLE service_request_updates RENAME CONSTRAINT case_updates_case_model_id_foreign TO service_request_updates_service_request_id_foreign');

        DB::commit();

        /**
         * Renaming the table cases to service_requests
         * Renaming the column case_number to service_request_number and case_form_submission_id to service_request_form_submission_id in table
        **/
        DB::beginTransaction();

        DB::statement('DROP VIEW IF EXISTS service_requests');

        DB::statement('ALTER TABLE cases RENAME TO service_requests');

        DB::statement('ALTER TABLE service_requests RENAME COLUMN case_number TO service_request_number');

        DB::statement('ALTER TABLE service_requests RENAME COLUMN case_form_submission_id TO service_request_form_submission_id');

        DB::statement('ALTER TABLE service_requests RENAME CONSTRAINT cases_pkey TO service_requests_pkey');

        DB::statement('ALTER TABLE service_requests RENAME CONSTRAINT cases_case_number_unique TO service_requests_service_request_number_unique');

        DB::statement('ALTER TABLE service_requests RENAME CONSTRAINT cases_created_by_id_foreign TO service_requests_created_by_id_foreign');

        DB::statement('ALTER TABLE service_requests RENAME CONSTRAINT cases_division_id_foreign TO service_requests_division_id_foreign');

        DB::statement('ALTER TABLE service_requests RENAME CONSTRAINT cases_priority_id_foreign TO service_requests_priority_id_foreign');

        DB::statement('ALTER TABLE service_requests RENAME CONSTRAINT cases_case_form_submission_id_foreign TO service_requests_service_request_form_submission_id_foreign');

        DB::statement('ALTER TABLE service_requests RENAME CONSTRAINT cases_status_id_foreign TO service_requests_status_id_foreign');

        DB::commit();
    }
};
