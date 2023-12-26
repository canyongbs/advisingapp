<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('serial_number');
            $table->string('name');
            $table->string('description');
            $table->foreignUuid('asset_type_id')->constrained('asset_types')->cascadeOnDelete();
            $table->foreignUuid('asset_status_id')->constrained('asset_statuses')->cascadeOnDelete();
            $table->foreignUuid('asset_location_id')->constrained('asset_locations')->cascadeOnDelete();
            $table->timestamp('purchase_date');
            $table->timestamps();
        });
    }
};
