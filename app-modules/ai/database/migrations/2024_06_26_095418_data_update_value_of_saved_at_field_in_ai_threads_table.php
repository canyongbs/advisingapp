<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $aiThreads = DB::table('ai_threads')->whereNotNull('name')->whereNull('saved_at')->get();

        $aiThreads->each(function ($thread, $key) {
            DB::table('ai_threads')->where('id', $thread->id)->update(['saved_at' => $thread->updated_at]);
        });
    }

    public function down(): void
    {
        $aiThreads = DB::table('ai_threads')->whereNotNull('name')->whereNotNull('saved_at')->get();

        $aiThreads->each(function ($thread, $key) {
            DB::table('ai_threads')->where('id', $thread->id)->update(['saved_at' => null]);
        });
    }
};
