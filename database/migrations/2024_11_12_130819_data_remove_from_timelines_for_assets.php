<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('timelines')->where('entity_type', 'asset')->delete();
    }

    public function down(): void {}
};
