<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('media')
            ->where('model_type', 'knowledge_base_article')
            ->update([
                'model_type' => 'resource_hub_article',
            ]);

        DB::table('media')
            ->where('model_type', 'knowledge_base_category')
            ->update([
                'model_type' => 'resource_hub_category',
            ]);

        DB::table('media')
            ->where('model_type', 'knowledge_base_quality')
            ->update([
                'model_type' => 'resource_hub_quality',
            ]);

        DB::table('media')
            ->where('model_type', 'knowledge_base_status')
            ->update([
                'model_type' => 'resource_hub_status',
            ]);
    }

    public function down(): void
    {
        DB::table('media')
            ->where('model_type', 'resource_hub_article')
            ->update([
                'model_type' => 'knowledge_base_article',
            ]);

        DB::table('media')
            ->where('model_type', 'resource_hub_category')
            ->update([
                'model_type' => 'knowledge_base_category',
            ]);

        DB::table('media')
            ->where('model_type', 'resource_hub_quality')
            ->update([
                'model_type' => 'knowledge_base_quality',
            ]);

        DB::table('media')
            ->where('model_type', 'resource_hub_status')
            ->update([
                'model_type' => 'knowledge_base_status',
            ]);
    }
};
