<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('roles')
            ->where('name', 'knowledge-base.knowledge_base_field_settings_management')
            ->update([
                'name' => 'resource-hub.resource_hub_field_settings_management',
                'updated_at' => now(),
            ]);

        DB::table('permissions')
            ->where('name', 'LIKE', 'knowledge_base_category.%')
            ->update([
                'name' => DB::raw("REPLACE(name, 'knowledge_base_category.', 'resource_hub_category.')"),
                'updated_at' => now(),
            ]);

        DB::table('permission_groups')
            ->where('name', 'Knowledge Base Category')
            ->update([
                'name' => 'Resource Hub Category',
                'updated_at' => now(),
            ]);

        DB::table('permissions')
            ->where('name', 'LIKE', 'knowledge_base_quality.%')
            ->update([
                'name' => DB::raw("REPLACE(name, 'knowledge_base_quality.', 'resource_hub_quality.')"),
                'updated_at' => now(),
            ]);

        DB::table('permission_groups')
            ->where('name', 'Knowledge Base Quality')
            ->update([
                'name' => 'Resource Hub Quality',
                'updated_at' => now(),
            ]);

        DB::table('permissions')
            ->where('name', 'LIKE', 'knowledge_base_status.%')
            ->update([
                'name' => DB::raw("REPLACE(name, 'knowledge_base_status.', 'resource_hub_status.')"),
                'updated_at' => now(),
            ]);

        DB::table('permission_groups')
            ->where('name', 'Knowledge Base Status')
            ->update([
                'name' => 'Resource Hub Status',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('roles')
            ->where('name', 'resource-hub.resource_hub_field_settings_management')
            ->update([
                'name' => 'knowledge-base.knowledge_base_field_settings_management',
                'updated_at' => now(),
            ]);

        DB::table('permissions')
            ->where('name', 'LIKE', 'resource_hub_category.%')
            ->update([
                'name' => DB::raw("REPLACE(name, 'resource_hub_category.', 'knowledge_base_category.')"),
                'updated_at' => now(),
            ]);

        DB::table('permission_groups')
            ->where('name', 'Resource Hub Category')
            ->update([
                'name' => 'Knowledge Base Category',
                'updated_at' => now(),
            ]);

        DB::table('permissions')
            ->where('name', 'LIKE', 'resource_hub_quality.%')
            ->update([
                'name' => DB::raw("REPLACE(name, 'resource_hub_quality.', 'knowledge_base_quality.')"),
                'updated_at' => now(),
            ]);

        DB::table('permission_groups')
            ->where('name', 'Resource Hub Quality')
            ->update([
                'name' => 'Knowledge Base Quality',
                'updated_at' => now(),
            ]);

        DB::table('permissions')
            ->where('name', 'LIKE', 'resource_hub_status.%')
            ->update([
                'name' => DB::raw("REPLACE(name, 'resource_hub_status.', 'knowledge_base_status.')"),
                'updated_at' => now(),
            ]);

        DB::table('permission_groups')
            ->where('name', 'Resource Hub Status')
            ->update([
                'name' => 'Knowledge Base Status',
                'updated_at' => now(),
            ]);
    }
};
