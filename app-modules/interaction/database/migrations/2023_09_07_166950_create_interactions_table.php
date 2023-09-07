<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('interactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users');
            $table->string('interactable_id')->nullable();
            $table->string('interactable_type')->nullable();
            $table->foreignUuid('type_id')->nullable()->constrained('interaction_types');
            $table->foreignUuid('campaign_id')->nullable()->constrained('interaction_campaigns');
            $table->foreignUuid('driver_id')->nullable()->constrained('interaction_drivers');
            $table->foreignUuid('status_id')->nullable()->constrained('interaction_statuses');
            $table->foreignUuid('outcome_id')->nullable()->constrained('interaction_outcomes');
            $table->timestamp('start_datetime');
            $table->timestamp('end_datetime')->nullable();
            $table->string('subject')->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }
};
