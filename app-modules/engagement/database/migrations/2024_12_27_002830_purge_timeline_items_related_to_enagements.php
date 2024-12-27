<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('timelines')
            ->where('entity_type', 'engagement')
            ->orWhere('entity_type', 'outbound_deliverable')
            ->delete();
    }

    public function down(): void
    {
        // No need to rollback
    }
};
