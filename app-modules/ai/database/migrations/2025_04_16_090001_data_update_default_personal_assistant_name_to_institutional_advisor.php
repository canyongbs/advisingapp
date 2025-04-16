<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('ai_assistants')
            ->where('is_default', true)
            ->update([
                'name' => 'Institutional Advisor',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('ai_assistants')
            ->where('is_default', true)
            ->update([
                'name' => 'Institutional Assistant',
                'updated_at' => now(),
            ]);
    }
};
