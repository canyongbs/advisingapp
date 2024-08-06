<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('tracked_event_counts')
            ->insertOrIgnore([
                'id' => (string) Str::orderedUuid(),
                'type' => 'ai-exchange',
                'count' => 0,
                'created_at' => now(),
            ]);

        DB::table('ai_messages')
            ->whereNotNull('message_id')
            ->eachById(function ($message) {
                DB::table('tracked_events')
                    ->insert([
                        'id' => (string) Str::orderedUuid(),
                        'type' => 'ai-exchange',
                        'occurred_at' => $message->created_at,
                    ]);

                DB::table('tracked_event_counts')
                    ->where('type', 'ai-exchange')
                    ->update([
                        'count' => DB::raw('count + 1'),
                        'last_occurred_at' => DB::raw("GREATEST(last_occurred_at, '{$message->created_at}')"),
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        DB::table('tracked_event_counts')
            ->where('type', 'ai-exchange')
            ->delete();

        DB::table('tracked_events')
            ->where('type', 'ai-exchange')
            ->delete();
    }
};
