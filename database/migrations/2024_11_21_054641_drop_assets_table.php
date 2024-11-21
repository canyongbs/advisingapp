<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('assets');
    }

    public function down(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('serial_number');
            $table->string('name');
            $table->longText('description');
            $table->foreignUuid('type_id')->constrained('asset_types');
            $table->foreignUuid('status_id')->constrained('asset_statuses');
            $table->foreignUuid('location_id')->constrained('asset_locations');
            $table->timestamp('purchase_date');

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
