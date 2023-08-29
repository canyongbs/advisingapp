<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('description');
            $table->string('status');
            $table->timestamp('due')->nullable();
            $table->foreignUuid('assigned_to')->nullable()->constrained('users');
            $table->string('concern_type')->nullable();
            $table->string('concern_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['concern_type', 'concern_id']);
        });
    }
};
