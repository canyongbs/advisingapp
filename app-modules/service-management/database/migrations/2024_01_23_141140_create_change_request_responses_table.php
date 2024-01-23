<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('change_request_responses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('change_request_id')->constrained('change_requests');
            $table->foreignUuid('user_id')->constrained('users');
            $table->boolean('approved');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
