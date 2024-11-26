<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('audit')
            ->where('auditable_type', 'service_request_assignment')
            ->update([
                'auditable_type' => 'case_assignment',
            ]);

        DB::table('audit')
            ->where('auditable_type', 'service_request_priority')
            ->update([
                'auditable_type' => 'case_priority',
            ]);

        DB::table('audit')
            ->where('auditable_type', 'service_request_status')
            ->update([
                'auditable_type' => 'case_status',
            ]);

        DB::table('audit')
            ->where('auditable_type', 'service_request_type')
            ->update([
                'auditable_type' => 'case_type',
            ]);

        DB::table('audit')
            ->where('auditable_type', 'service_request_update')
            ->update([
                'auditable_type' => 'case_update',
            ]);

        DB::table('audit')
            ->where('auditable_type', 'service_request')
            ->update([
                'auditable_type' => 'case_model',
            ]);
    }

    public function down(): void
    {
        DB::table('audit')
            ->where('auditable_type', 'case_assignment')
            ->update([
                'auditable_type' => 'service_request_assignment',
            ]);

        DB::table('audit')
            ->where('auditable_type', 'case_priority')
            ->update([
                'auditable_type' => 'service_request_priority',
            ]);

        DB::table('audit')
            ->where('auditable_type', 'case_status')
            ->update([
                'auditable_type' => 'service_request_status',
            ]);

        DB::table('audit')
            ->where('auditable_type', 'case_type')
            ->update([
                'auditable_type' => 'service_request_type',
            ]);

        DB::table('audit')
            ->where('auditable_type', 'case_update')
            ->update([
                'auditable_type' => 'service_request_update',
            ]);

        DB::table('audit')
            ->where('auditable_type', 'case_model')
            ->update([
                'auditable_type' => 'service_request',
            ]);
    }
};
