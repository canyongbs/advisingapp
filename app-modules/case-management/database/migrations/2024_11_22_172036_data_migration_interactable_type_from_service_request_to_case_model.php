<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('interactions')->where('interactable_type', 'service_request')->update([
            'interactable_type' => 'case_model',
        ]);
    }

    public function down(): void
    {
        DB::table('interactions')->where('interactable_type', 'case_model')->update([
            'interactable_type' => 'service_request',
        ]);
    }
};
