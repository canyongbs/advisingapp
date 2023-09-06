<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('engagements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('engagement_batch_id')->nullable()->constrained('engagement_batches');
            $table->string('recipient_id')->nullable();
            $table->string('recipient_type')->nullable();
            $table->string('subject');
            $table->longText('description')->nullable();
            $table->timestamp('deliver_at');
            $table->timestamps();
        });
    }
};
