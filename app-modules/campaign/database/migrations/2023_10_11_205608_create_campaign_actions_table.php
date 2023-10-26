<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('campaign_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('campaign_id')->constrained('campaigns');
            $table->string('type');
            $table->json('data');
            $table->timestamp('execute_at');
            $table->timestamp('last_execution_attempt_at')->nullable();
            $table->text('last_execution_attempt_error')->nullable();
            $table->timestamp('successfully_executed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
