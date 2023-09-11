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
            $table->foreignUuid('interaction_type_id')->nullable()->constrained('interaction_types');
            $table->foreignUuid('interaction_relation_id')->nullable()->constrained('interaction_relations');
            $table->foreignUuid('interaction_campaign_id')->nullable()->constrained('interaction_campaigns');
            $table->foreignUuid('interaction_driver_id')->nullable()->constrained('interaction_drivers');
            $table->foreignUuid('interaction_status_id')->nullable()->constrained('interaction_statuses');
            $table->foreignUuid('interaction_outcome_id')->nullable()->constrained('interaction_outcomes');
            $table->foreignUuid('interaction_institution_id')->nullable()->constrained('interaction_institutions');
            $table->timestamp('start_datetime');
            $table->timestamp('end_datetime')->nullable();
            $table->string('subject')->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }
};
