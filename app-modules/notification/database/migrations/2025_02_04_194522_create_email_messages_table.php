<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('email_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('notification_class');
            $table->string('external_reference_id')->nullable()->unique();
            $table->jsonb('content');
            $table->unsignedInteger('quota_usage')->default(0);
            $table->nullableUuidMorphs('related');
            $table->string('recipient_id')->nullable();
            $table->string('recipient_type')->nullable();
            $table->timestamps();

            $table->index(['recipient_id', 'recipient_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_messages');
    }
};
