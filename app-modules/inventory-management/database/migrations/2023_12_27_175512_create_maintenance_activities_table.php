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
            // It's possible that the maintenance provider can be an already known entity within the system as well, like a user
            // For now, we will assume that it is a separate and new entity specifically for this purpose.
            $table->foreignUuid('maintenance_provider_id')->nullable()->constrained('maintenance_providers');
            // We might want to standardize the details of a maintenance activity, so it can be easily replicated with consistency
            $table->string('details');
            $table->timestamp('scheduled_date')->nullable();
            $table->timestamp('completed_date')->nullable();
            $table->string('status')->default(MaintenanceActivityStatus::Scheduled);
            $table->longText('notes')->nullable();
            $table->timestamps();
        });
    }
};
