<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('notification_settings_pivot', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('notification_setting_id')->constrained('notification_settings');
            $table->uuidMorphs('related_to');

            $table->timestamps();

            $table->unique(['notification_setting_id', 'related_to_type', 'related_to_id']);
        });
    }
};
