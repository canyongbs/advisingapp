<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('workflow_proactive_alert_details', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->text('description');
            $table->string('severity');
            $table->text('suggested_intervention');
            $table->foreignUuid('status_id')->nullable()->constrained('alert_statuses');
            $table->foreignUuid('workflow_step_id')->constrained('workflow_steps');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_proactive_alert_details');
    }
};
