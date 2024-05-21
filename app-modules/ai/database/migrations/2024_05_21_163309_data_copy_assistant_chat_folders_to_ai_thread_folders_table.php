<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('assistant_chat_folders')
            ->get(['id', 'name', 'user_id', 'created_at', 'updated_at', 'deleted_at'])
            ->each(function (stdClass $folder) {
                DB::table('ai_thread_folders')
                    ->insert([
                        'id' => $folder->id,
                        'name' => $folder->name,
                        'application' => 'personal_assistant',
                        'user_id' => $folder->user_id,
                        'created_at' => $folder->created_at,
                        'updated_at' => $folder->updated_at,
                        'deleted_at' => $folder->deleted_at,
                    ]);
            });
    }

    public function down(): void
    {
        DB::table('ai_thread_folders')->truncate();
    }
};
