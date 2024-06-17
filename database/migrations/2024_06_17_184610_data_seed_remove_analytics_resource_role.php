<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('roles')->where('name', 'analytics.analytics_management')->delete();
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
