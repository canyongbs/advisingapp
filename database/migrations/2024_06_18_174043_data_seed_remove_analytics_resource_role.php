<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $role_details = DB::table('roles')->where('name', 'analytics.analytics_management')->first();

        if (! empty($role_details)) {
            DB::table('model_has_roles')->where('role_id', $role_details->id)->delete();
            DB::table('role_has_permissions')->where('role_id', $role_details->id)->delete();
            DB::table('roles')->where('name', 'analytics.analytics_management')->delete();
        }
    }

    public function down(): void
    {
        $analytics_role = [
            'id' => (string) Str::orderedUuid(),
            'name' => 'analytics.analytics_management',
            'guard_name' => 'web',
            'created_at' => now(),
        ];
        DB::table('roles')->insert($analytics_role);
    }
};
