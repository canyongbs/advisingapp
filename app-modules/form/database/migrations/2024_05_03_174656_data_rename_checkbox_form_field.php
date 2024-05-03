<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('form_fields')
            ->where('type', 'checkbox')
            ->update(['type' => 'agreement']);

        DB::table('forms')
            ->where('content', 'like', '%"type":"checkbox"%')
            ->eachById(function ($form) {
                DB::table('forms')
                    ->where('id', $form->id)
                    ->update([
                        'content' => str_replace('"type":"checkbox"', '"type":"agreement"', $form->content),
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        DB::table('form_fields')
            ->where('type', 'agreement')
            ->update(['type' => 'checkbox']);

        DB::table('forms')
            ->where('content', 'like', '%"type":"agreement"%')
            ->eachById(function ($form) {
                DB::table('forms')
                    ->where('id', $form->id)
                    ->update([
                        'content' => str_replace('"type":"agreement"', '"type":"checkbox"', $form->content),
                        'updated_at' => now(),
                    ]);
            });
    }
};
