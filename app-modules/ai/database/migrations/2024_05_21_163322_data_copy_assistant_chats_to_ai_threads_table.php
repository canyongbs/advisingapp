<?php

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifySettings;

return new class () extends Migration {
    use CanModifySettings;

    public function up(): void
    {
        if (
            DB::table('assistant_chats')->count() &&
            (! DB::table('ai_assistants')
                ->where('is_default', true)
                ->count())
        ) {
            $tenant = Tenant::current();

            $assistantId = $this->getSetting('ai', 'assistant_id');

            if (blank($assistantId)) {
                throw new Exception('No default AI assistant ID was found in the settings, but chat threads exist.');
            }

            DB::table('ai_assistants')
                ->insert([
                    'id' => (string) Str::orderedUuid(),
                    'name' => "{$tenant->name} AI Assistant",
                    'description' => "An AI Assistant for {$tenant->name}",
                    'instructions' => $this->getSetting('ai', 'prompt_system_context'),
                    'application' => 'personal_assistant',
                    'model' => 'openai_gpt_3.5',
                    'assistant_id' => $assistantId,
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        $defaultAiAssistantId = DB::table('ai_assistants')
            ->where('is_default', true)
            ->value('id');

        DB::table('assistant_chats')
            ->get(['id', 'name', 'user_id', 'assistant_chat_folder_id', 'created_at', 'updated_at', 'deleted_at', 'thread_id', 'ai_assistant_id'])
            ->each(function (stdClass $thread) use ($defaultAiAssistantId) {
                DB::table('ai_threads')
                    ->insert([
                        'id' => $thread->id,
                        'thread_id' => $thread->thread_id,
                        'name' => $thread->name,
                        'assistant_id' => $thread->ai_assistant_id ?? $defaultAiAssistantId,
                        'folder_id' => $thread->assistant_chat_folder_id,
                        'user_id' => $thread->user_id,
                        'created_at' => $thread->created_at,
                        'updated_at' => $thread->updated_at,
                        'deleted_at' => $thread->deleted_at,
                    ]);
            });
    }

    public function down(): void
    {
        DB::table('ai_threads')->truncate();
    }
};
