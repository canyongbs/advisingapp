<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('change_request_statuses');
    }

    public function down(): void
    {
        Schema::create('change_request_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('classification');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
