<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::statement('ALTER TABLE engagements ALTER COLUMN subject TYPE jsonb USING subject::jsonb');
        DB::statement('ALTER TABLE engagement_batches ALTER COLUMN subject TYPE jsonb USING subject::jsonb');
    }

    public function down(): void
    {
        Schema::table('engagements', function (Blueprint $table) {
            $table->text('subject')->change();
        });

        Schema::table('engagement_batches', function (Blueprint $table) {
            $table->text('subject')->change();
        });
    }
};
