<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permission_group = DB::table('permission_groups')
            ->where('name', 'Report')->first();

        if(!empty($permission_group)) {
            DB::table('permissions')->insert(
                [
                    'id' => (string) Str::orderedUuid(),
                    'group_id' => $permission_group->id,
                    'guard_name' => 'web',
                    'name' => 'report-library.view-any',
                    'created_at' => now(),
                ]
            );
        }
    }
};
