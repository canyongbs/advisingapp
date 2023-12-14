<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('application_submission_states', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('classification');
            $table->string('name');
            $table->string('color');
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
