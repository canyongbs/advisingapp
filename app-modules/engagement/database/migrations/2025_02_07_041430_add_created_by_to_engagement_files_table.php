<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('engagement_files', function (Blueprint $table) {
            $table->nullableUuidMorphs('created_by');
        });
    }

    public function down(): void
    {
        Schema::table('engagement_files', function (Blueprint $table) {
            $table->dropMorphs('created_by');
        });
    }
};
