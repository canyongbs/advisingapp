<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('ai_assistants')
            ->update([
                'application' => 'personal_assistant',
                'is_default' => new Expression('CASE WHEN ai_assistants.type = \'default\' THEN true ELSE false END'),
                'model' => 'openai_gpt_3.5',
            ]);
    }

    public function down(): void
    {
        DB::table('ai_assistants')
            ->update([
                'application' => null,
                'is_default' => false,
                'model' => null,
            ]);
    }
};
