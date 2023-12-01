<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Assist\ServiceManagement\Enums\ServiceRequestAssignmentStatus;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('service_request_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_request_id')->constrained('service_requests');
            $table->foreignUuid('user_id')->constrained('users');
            $table->timestamp('assigned_at')->nullable();
            $table->string('status')->default(ServiceRequestAssignmentStatus::Active);
            $table->timestamps();
        });
    }
};
