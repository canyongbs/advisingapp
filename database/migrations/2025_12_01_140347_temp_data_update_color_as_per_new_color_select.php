<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('notification_settings')->whereNotNull('primary_color')->whereIn('primary_color', ['slate', 'zinc', 'neutral', 'stone'])->update(['primary_color' => 'gray']);

            DB::table('applications')->whereNotNull('primary_color')->whereIn('primary_color', ['slate', 'zinc', 'neutral', 'stone'])->update(['primary_color' => 'gray']);

            DB::table('case_forms')->whereNotNull('primary_color')->whereIn('primary_color', ['slate', 'zinc', 'neutral', 'stone'])->update(['primary_color' => 'gray']);

            DB::table('event_registration_forms')->whereNotNull('primary_color')->whereIn('primary_color', ['slate', 'zinc', 'neutral', 'stone'])->update(['primary_color' => 'gray']);

            DB::table('surveys')->whereNotNull('primary_color')->whereIn('primary_color', ['slate', 'zinc', 'neutral', 'stone'])->update(['primary_color' => 'gray']);
        });
    }
};
