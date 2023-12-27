<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use AdvisingApp\InventoryManagement\Enums\MaintenanceActivityStatus;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('maintenance_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('asset_id')->constrained('assets');
            $table->foreignUuid('maintenance_provider_id')->nullable()->constrained('maintenance_providers');
            $table->timestamp('date');
            $table->timestamp('scheduled_date');
            $table->string('status')->default(MaintenanceActivityStatus::Scheduled);
            $table->longText('notes')->nullable();
            $table->timestamps();
        });
    }
};
