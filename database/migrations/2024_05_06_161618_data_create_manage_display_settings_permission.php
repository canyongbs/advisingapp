<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('permission_groups')
            ->insert([
                'id' => $groupId = Str::orderedUuid(),
                'name' => 'Display Settings',
                'created_at' => now(),
            ]);

        DB::table('permissions')
            ->insert([
                'id' => Str::orderedUuid(),
                'name' => 'display_settings.manage',
                'guard_name' => 'web',
                'group_id' => $groupId,
                'created_at' => now(),
            ]);
    }
};
