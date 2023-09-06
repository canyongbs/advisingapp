<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('service_request_updates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_request_id')->nullable()->constrained('service_requests');
            $table->text('update');
            $table->boolean('internal');
            $table->string('direction');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
