<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('assistant_chat_messages')->truncate();
            DB::table('assistant_chats')->truncate();
        });
    }
};
