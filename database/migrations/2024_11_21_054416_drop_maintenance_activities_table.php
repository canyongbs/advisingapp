<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('maintenance_activities');
    }

    public function down(): void
    {
        Schema::create('maintenance_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('asset_id')->constrained('assets');
            $table->foreignUuid('maintenance_provider_id')->nullable()->constrained('maintenance_providers');
            $table->string('details');
            $table->timestamp('scheduled_date')->nullable();
            $table->timestamp('completed_date')->nullable();
            $table->string('status')->default('scheduled');
            $table->longText('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
