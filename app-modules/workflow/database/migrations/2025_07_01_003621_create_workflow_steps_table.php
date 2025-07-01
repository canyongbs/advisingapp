<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('details_id');
            $table->string('details_type');
            $table->integer('delay_minutes')->default(0);
            $table->foreignUuid('workflow_id')->constrained('workflow');
            $table->foreignUuid('previous_step_id')->nullable()->constrained('workflow_steps');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
    }
};
