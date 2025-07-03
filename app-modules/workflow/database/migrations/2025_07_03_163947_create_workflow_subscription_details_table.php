<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_subscription_details', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->jsonb('user_ids');
            $table->boolean('remove_prior');
            $table->foreignUuid('workflow_step_id')->constrained('workflow_steps');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_subscription_details');
    }
};
