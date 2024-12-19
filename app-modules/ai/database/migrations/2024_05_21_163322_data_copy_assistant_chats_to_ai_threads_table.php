<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Models\Tenant;
use Database\Migrations\Concerns\CanModifySettings;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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
