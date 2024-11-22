<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

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
