<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('concern_type');
            $table->string('concern_id');
            $table->text('description');
            $table->string('severity');
            $table->string('status');
            $table->text('suggested_intervention');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['concern_type', 'concern_id']);
        });
    }
};
