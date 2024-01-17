<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('change_request_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('classification');
            $table->timestamps();
        });
    }
};
