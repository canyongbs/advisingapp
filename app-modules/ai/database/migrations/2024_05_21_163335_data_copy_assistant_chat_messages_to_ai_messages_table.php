<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('assistant_chat_messages')
            ->leftJoin('assistant_chats', 'assistant_chat_messages.assistant_chat_id', '=', 'assistant_chats.id')
            ->get(['assistant_chat_messages.id', 'assistant_chat_id', 'from', 'message', 'assistant_chat_messages.name', 'user_id', 'assistant_chat_messages.created_at', 'assistant_chat_messages.updated_at', 'assistant_chat_messages.deleted_at', 'message_id'])
            ->each(function (stdClass $message) {
                DB::table('ai_messages')
                    ->insert([
                        'id' => $message->id,
                        'message_id' => $message->message_id,
                        'content' => $message->message,
                        'thread_id' => $message->assistant_chat_id,
                        'user_id' => ($message->from === 'user') ? $message->user_id : null,
                        'created_at' => $message->created_at,
                        'updated_at' => $message->updated_at,
                        'deleted_at' => $message->deleted_at,
                    ]);
            });
    }

    public function down(): void
    {
        DB::table('ai_messages')->truncate();
    }
};
