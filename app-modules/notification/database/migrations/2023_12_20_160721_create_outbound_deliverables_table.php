<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('outbound_deliverables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('related_id')->nullable();
            $table->string('related_type')->nullable();
            $table->string('recipient_id')->nullable();
            $table->string('recipient_type')->nullable();
            $table->string('channel');
            $table->string('external_reference_id')->nullable()->unique();
            $table->string('external_status')->nullable();
            // TODO An array of the content that the message consisted of...
            $table->json('content')->nullable();
            $table->string('delivery_status')->default(NotificationDeliveryStatus::Awaiting);
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('last_delivery_attempt')->nullable();
            $table->longText('delivery_response')->nullable();
            $table->timestamps();
        });
    }
};
