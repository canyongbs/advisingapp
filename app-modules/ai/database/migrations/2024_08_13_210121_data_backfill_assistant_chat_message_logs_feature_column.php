<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('assistant_chat_message_logs')
            ->whereNull('feature')
            ->update(['feature' => 'conversations']);
    }

    public function down(): void
    {
        DB::table('assistant_chat_message_logs')
            ->update(['feature' => null]);
    }
};
