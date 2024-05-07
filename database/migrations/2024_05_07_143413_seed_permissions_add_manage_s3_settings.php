<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $groupId = (string) Str::orderedUuid();

        DB::table('permission_groups')
            ->insert(
                [
                    'id' => $groupId,
                    'name' => 'Amazon S3',
                    'created_at' => now(),
                ]
            );

        DB::table('permissions')->insert(
            [
                'id' => (string) Str::orderedUuid(),
                'group_id' => $groupId,
                'guard_name' => 'web',
                'name' => 'amazon-s3.manage_s3_settings',
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('permissions')
            ->where('name', 'amazon-s3.manage_s3_settings')
            ->delete();

        DB::table('permission_groups')
            ->where('name', 'Amazon S3')
            ->delete();
    }
};
