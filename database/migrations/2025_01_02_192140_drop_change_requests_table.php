<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('change_requests');
    }

    public function down(): void
    {
        Schema::create('change_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->foreignUuid('change_request_type_id')->constrained('change_request_types');
            $table->foreignUuid('change_request_status_id')->constrained('change_request_statuses');
            $table->string('title');
            $table->text('description');
            $table->longText('reason');
            $table->longText('backout_strategy');
            $table->integer('impact');
            $table->integer('likelihood');
            $table->integer('risk_score');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
