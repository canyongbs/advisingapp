<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tracked_events', function (Blueprint $table) {
            $table->nullableUuidMorphs('related_to');
        });
    }

    public function down(): void
    {
        Schema::table('tracked_events', function (Blueprint $table) {
            $table->dropIndex(['related_to_type', 'related_to_id']);
            $table->dropColumn(['related_to_type', 'related_to_id']);
        });
    }
};
