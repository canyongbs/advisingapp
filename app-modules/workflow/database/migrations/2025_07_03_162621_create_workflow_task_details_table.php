<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_task_details', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('title');
            $table->longText('description');
            $table->timestamp('due')->nullable();
            $table->foreignUuid('assigned_to')->nullable()->constrained('users');
            $table->foreignUuid('workflow_step_id')->constrained('workflow_steps');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_task_details');
    }
};
