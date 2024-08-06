<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('tracked_event_counts')
            ->insertOrIgnore([
                'id' => (string) Str::orderedUuid(),
                'type' => 'ai-thread-saved',
                'count' => 0,
                'created_at' => now(),
            ]);

        DB::table('ai_threads')
            ->whereNotNull('saved_at')
            ->eachById(function ($thread) {
                DB::table('tracked_events')
                    ->insert([
                        'id' => (string) Str::orderedUuid(),
                        'type' => 'ai-thread-saved',
                        'occurred_at' => $thread->saved_at,
                    ]);

                DB::table('tracked_event_counts')
                    ->where('type', 'ai-thread-saved')
                    ->update([
                        'count' => DB::raw('count + 1'),
                        'last_occurred_at' => DB::raw("GREATEST(last_occurred_at, '{$thread->saved_at}')"),
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        DB::table('tracked_event_counts')
            ->where('type', 'ai-thread-saved')
            ->delete();

        DB::table('tracked_events')
            ->where('type', 'ai-thread-saved')
            ->delete();
    }
};
