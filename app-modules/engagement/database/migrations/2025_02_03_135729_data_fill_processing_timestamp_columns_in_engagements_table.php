<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('engagements')
            ->update([
                'scheduled_at' => DB::raw('deliver_at'),
                'dispatched_at' => DB::raw('deliver_at'),
            ]);
    }

    public function down(): void
    {
        DB::table('engagements')
            ->update([
                'scheduled_at' => null,
                'dispatched_at' => null,
            ]);
    }
};
