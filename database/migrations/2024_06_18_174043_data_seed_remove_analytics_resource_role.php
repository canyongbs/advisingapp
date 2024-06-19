<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $role_details = DB::table('roles')->where('name', 'analytics.analytics_management')->whereIn('guard_name', ['web', 'api'])->get();

        if (! $role_details->isEmpty()) {
            foreach ($role_details as $role) {
                DB::table('model_has_roles')->where('role_id', $role->id)->delete();
                DB::table('role_has_permissions')->where('role_id', $role->id)->delete();
                DB::table('roles')->where('name', $role->name)->delete();
            }
        }
    }

    public function down(): void
    {
        $analytics_roles = [
            [
                'id' => (string) Str::orderedUuid(),
                'name' => 'analytics.analytics_management',
                'guard_name' => 'web',
                'created_at' => now(),
            ],
            [
                'id' => (string) Str::orderedUuid(),
                'name' => 'analytics.analytics_management',
                'guard_name' => 'api',
                'created_at' => now(),
            ]
        ];
        DB::table('roles')->insert($analytics_roles);
    }
};
