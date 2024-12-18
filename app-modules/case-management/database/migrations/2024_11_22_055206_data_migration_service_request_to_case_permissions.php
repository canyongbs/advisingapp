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
        DB::transaction(function () {
            DB::table('permissions')
                ->where('name', 'LIKE', 'service_request.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'service_request.', 'case.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Service Request')
                ->update([
                    'name' => 'Case',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'service_request_assignment.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'service_request_assignment.', 'case_assignment.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Service Request Assignment')
                ->update([
                    'name' => 'Case Assignment',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'service_request_form.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'service_request_form.', 'case_form.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Service Request Form')
                ->update([
                    'name' => 'Case Form',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'service_request_history.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'service_request_history.', 'case_history.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Service Request History')
                ->update([
                    'name' => 'Case History',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'service_request_priority.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'service_request_priority.', 'case_priority.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Service Request Priority')
                ->update([
                    'name' => 'Case Priority',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'service_request_status.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'service_request_status.', 'case_status.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Service Request Status')
                ->update([
                    'name' => 'Case Status',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'service_request_type.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'service_request_type.', 'case_type.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Service Request Type')
                ->update([
                    'name' => 'Case Type',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'service_request_update.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'service_request_update.', 'case_update.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Service Request Update')
                ->update([
                    'name' => 'Case Update',
                    'updated_at' => now(),
                ]);
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::table('permissions')
                ->where('name', 'LIKE', 'case.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'case.', 'service_request.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Case')
                ->update([
                    'name' => 'Service Request',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'case_assignment.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'case_assignment.', 'service_request_assignment.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Case Assignment')
                ->update([
                    'name' => 'Service Request Assignment',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'case_form.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'case_form.', 'service_request_form.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Case Form')
                ->update([
                    'name' => 'Service Request Form',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'case_history.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'case_history.', 'service_request_history.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Case History')
                ->update([
                    'name' => 'Service Request History',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'case_priority.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'case_priority.', 'service_request_priority.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Case Priority')
                ->update([
                    'name' => 'Service Request Priority',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'case_status.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'case_status.', 'service_request_status.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Case Status')
                ->update([
                    'name' => 'Service Request Status',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'case_type.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'case_type.', 'service_request_type.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Case Type')
                ->update([
                    'name' => 'Service Request Type',
                    'updated_at' => now(),
                ]);

            DB::table('permissions')
                ->where('name', 'LIKE', 'case_update.%')
                ->update([
                    'name' => DB::raw("REPLACE(name, 'case_update.', 'service_request_update.')"),
                    'updated_at' => now(),
                ]);

            DB::table('permission_groups')
                ->where('name', 'Case Update')
                ->update([
                    'name' => 'Service Request Update',
                    'updated_at' => now(),
                ]);
        });
    }
};
