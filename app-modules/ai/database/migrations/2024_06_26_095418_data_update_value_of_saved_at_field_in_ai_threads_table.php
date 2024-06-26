<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $ai_threads = DB::table('ai_threads')->whereNotNull('name')->whereNull('saved_at')->get();

        if (! $ai_threads->isEmpty()) {
            foreach ($ai_threads as $ai_thread) {
                DB::table('ai_threads')->where('id', $ai_thread->id)->update(['saved_at' => $ai_thread->updated_at]);
            }
        }
    }

    public function down(): void
    {
        $ai_threads = DB::table('ai_threads')->whereNotNull('name')->whereNotNull('saved_at')->get();

        if (! $ai_threads->isEmpty()) {
            foreach ($ai_threads as $ai_thread) {
                DB::table('ai_threads')->where('id', $ai_thread->id)->update(['saved_at' => null]);
            }
        }
    }
};
