<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('outbound_deliverables');
    }

    public function down(): void
    {
        Schema::create('outbound_deliverables', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('channel');
            $table->string('notification_class');
            $table->string('external_reference_id')->nullable()->unique();
            $table->string('external_status')->nullable();
            $table->json('content')->nullable();
            $table->string('delivery_status')->default('processing');
            $table->longText('delivery_response')->nullable();
            $table->integer('quota_usage')->default(0);

            $table->string('related_id')->nullable();
            $table->string('related_type')->nullable();
            $table->string('recipient_id')->nullable();
            $table->string('recipient_type')->nullable();

            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('last_delivery_attempt')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
