<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Assist\Engagement\Enums\EngagementDeliveryStatus;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('engagement_deliverables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('engagement_id')->constrained('engagements');
            $table->string('channel');
            $table->string('delivery_status')->default(EngagementDeliveryStatus::AWAITING->value);
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('last_delivery_attempt')->nullable();
            $table->longText('delivery_response')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
