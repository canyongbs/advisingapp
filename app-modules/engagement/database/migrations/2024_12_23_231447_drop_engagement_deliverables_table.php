<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('engagement_deliverables');
    }

    public function down(): void
    {
        Schema::create('engagement_deliverables', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('engagement_id')->constrained('engagements')->unique();
            $table->string('channel');
            $table->string('external_reference_id')->nullable()->unique();
            $table->string('external_status')->nullable();
            $table->string('delivery_status')->default('awaiting');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('last_delivery_attempt')->nullable();
            $table->longText('delivery_response')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
