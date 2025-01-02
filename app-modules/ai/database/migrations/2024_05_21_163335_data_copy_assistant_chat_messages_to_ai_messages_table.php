<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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
