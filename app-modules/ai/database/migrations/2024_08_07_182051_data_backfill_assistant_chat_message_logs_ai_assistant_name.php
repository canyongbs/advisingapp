<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $defaultAssistant = DB::table('ai_assistants')
            ->where('is_default', true)
            ->first();

        $assistantName = $defaultAssistant?->name ?? 'Institutional Assistant';

        DB::table('assistant_chat_message_logs')
            ->whereNull('ai_assistant_name')
            ->orWhere('ai_assistant_name', '')
            ->eachById(function ($log) use ($assistantName) {
                $message = DB::table('ai_messages')
                    ->where('content', $log->message)
                    ->where('created_at', $log->sent_at)
                    ->first();

                if (! empty($message)) {
                    $thread = DB::table('ai_threads')
                        ->where('id', $message->thread_id)
                        ->first();

                    if (! empty($thread)) {
                        $assistant = DB::table('ai_assistants')
                            ->where('id', $thread->assistant_id)
                            ->first();

                        if (! empty($assistant)) {
                            $assistantName = $assistant->name;
                        }
                    }

                    DB::table('assistant_chat_message_logs')
                        ->where('id', $log->id)
                        ->update(['ai_assistant_name' => $assistantName]);
                }
            });
    }

    public function down(): void
    {
        DB::table('assistant_chat_message_logs')
            ->eachById(function ($log) {
                DB::table('assistant_chat_message_logs')
                    ->where('id', $log->id)
                    ->update(['ai_assistant_name' => 'Institutional Assistant']);
            });
    }
};
