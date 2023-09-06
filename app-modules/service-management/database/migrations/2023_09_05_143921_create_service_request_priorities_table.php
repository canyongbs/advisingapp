<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('service_request_priorities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('order');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
