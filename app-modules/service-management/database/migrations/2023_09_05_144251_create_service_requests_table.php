<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('service_request_number')->unique();
            $table->string('respondent_type')->nullable();
            $table->string('respondent_id')->nullable();
            $table->longText('close_details')->nullable();
            $table->longText('res_details')->nullable();
            $table->foreignUuid('institution_id')->nullable()->constrained('institutions');
            $table->foreignUuid('status_id')->nullable()->constrained('service_request_statuses');
            $table->foreignUuid('type_id')->nullable()->constrained('service_request_types');
            $table->foreignUuid('priority_id')->nullable()->constrained('service_request_priorities');
            $table->foreignUuid('assigned_to_id')->nullable()->constrained('users');
            $table->foreignUuid('created_by_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
