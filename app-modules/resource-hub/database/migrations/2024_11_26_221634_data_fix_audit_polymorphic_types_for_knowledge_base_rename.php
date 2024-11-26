<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('audits')
            ->where('auditable_type', 'knowledge_base_article')
            ->update([
                'auditable_type' => 'resource_hub_article',
            ]);

        DB::table('audits')
            ->where('auditable_type', 'knowledge_base_category')
            ->update([
                'auditable_type' => 'resource_hub_category',
            ]);

        DB::table('audits')
            ->where('auditable_type', 'knowledge_base_quality')
            ->update([
                'auditable_type' => 'resource_hub_quality',
            ]);

        DB::table('audits')
            ->where('auditable_type', 'knowledge_base_status')
            ->update([
                'auditable_type' => 'resource_hub_status',
            ]);
    }

    public function down(): void
    {
        DB::table('audits')
            ->where('auditable_type', 'resource_hub_article')
            ->update([
                'auditable_type' => 'knowledge_base_article',
            ]);

        DB::table('audits')
            ->where('auditable_type', 'resource_hub_category')
            ->update([
                'auditable_type' => 'knowledge_base_category',
            ]);

        DB::table('audits')
            ->where('auditable_type', 'resource_hub_quality')
            ->update([
                'auditable_type' => 'knowledge_base_quality',
            ]);

        DB::table('audits')
            ->where('auditable_type', 'resource_hub_status')
            ->update([
                'auditable_type' => 'knowledge_base_status',
            ]);
    }
};
