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
            // TODO Might change these to lose the interaction prefix, but would have to change relationships
            $table->foreignUuid('interaction_type_id')->nullable()->constrained('interaction_types');
            $table->foreignUuid('interaction_campaign_id')->nullable()->constrained('interaction_campaigns');
            $table->foreignUuid('interaction_driver_id')->nullable()->constrained('interaction_drivers');
            $table->foreignUuid('interaction_status_id')->nullable()->constrained('interaction_statuses');
            $table->foreignUuid('interaction_outcome_id')->nullable()->constrained('interaction_outcomes');
            // TODO I need to figure out how institutions will work here
            // $table->foreignUuid('institution_id')->nullable()->constrained('institutions');
            $table->timestamp('start_datetime');
            $table->timestamp('end_datetime')->nullable();
            $table->string('subject')->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }
};
