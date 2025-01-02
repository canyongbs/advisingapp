<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('change_request_responses');
    }

    public function down(): void
    {
        Schema::create('change_request_responses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('change_request_id')->constrained('change_requests')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users');
            $table->boolean('approved');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
