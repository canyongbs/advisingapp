<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('caseload_id')->constrained('caseloads');
            $table->string('name');
            $table->dateTimeTz('execution_time');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
