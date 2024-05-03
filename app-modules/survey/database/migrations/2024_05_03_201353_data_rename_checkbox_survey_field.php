<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('survey_fields')
            ->where('type', 'checkbox')
            ->update(['type' => 'checkboxes']);

        DB::table('surveys')
            ->where('content', 'like', '%"type":"checkbox"%')
            ->eachById(function ($survey) {
                DB::table('surveys')
                    ->where('id', $survey->id)
                    ->update([
                        'content' => str_replace('"type":"checkbox"', '"type":"checkboxes"', $survey->content),
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        DB::table('survey_fields')
            ->where('type', 'checkboxes')
            ->update(['type' => 'checkbox']);

        DB::table('surveys')
            ->where('content', 'like', '%"type":"checkboxes"%')
            ->eachById(function ($survey) {
                DB::table('surveys')
                    ->where('id', $survey->id)
                    ->update([
                        'content' => str_replace('"type":"checkboxes"', '"type":"checkbox"', $survey->content),
                        'updated_at' => now(),
                    ]);
            });
    }
};
