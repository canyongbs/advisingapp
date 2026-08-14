<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            Schema::dropIfExists('notification_settings');
            Schema::dropIfExists('notification_settings_pivot');
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            Schema::create('notification_settings', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('name');
                $table->string('from_name')->nullable();
                $table->string('primary_color')->nullable();
                $table->longText('description')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('notification_settings_pivot', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->foreignUuid('notification_setting_id')->constrained('notification_settings');
                $table->uuidMorphs('related_to');

                $table->timestamps();

                $table->unique(['notification_setting_id', 'related_to_type', 'related_to_id']);
            });
        });
    }
};
