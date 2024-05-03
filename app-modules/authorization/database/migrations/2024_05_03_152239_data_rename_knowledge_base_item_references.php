<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('permissions')
            ->where('name', 'LIKE', 'knowledge_base_item.%')
            ->update([
                'name' => DB::raw("REPLACE(name, 'knowledge_base_item.', 'knowledge_base_article.')"),
                'updated_at' => now(),
            ]);

        DB::table('permission_groups')
            ->where('name', 'Knowledge Base Item')
            ->update([
                'name' => 'Knowledge Base Article',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('permissions')
            ->where('name', 'LIKE', 'knowledge_base_article.%')
            ->update([
                'name' => DB::raw("REPLACE(name, 'knowledge_base_article.', 'knowledge_base_item.')"),
                'updated_at' => now(),
            ]);

        DB::table('permission_groups')
            ->where('name', 'Knowledge Base Article')
            ->update([
                'name' => 'Knowledge Base Item',
                'updated_at' => now(),
            ]);
    }
};
