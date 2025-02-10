<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('timelines')
            ->where('timelineable_type', 'outbound_deliverable')
            ->delete();
    }

    public function down(): void
    {
        // There is no way to reverse this
    }
};
