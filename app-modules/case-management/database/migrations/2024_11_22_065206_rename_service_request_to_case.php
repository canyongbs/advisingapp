<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
