<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            // TODO: Change to UUID
            $table->id();
            $table->string('name');
            $table->string('domain')->unique();
            $table->text('key');
            $table->text('config');
            $table->timestamps();
        });
    }
};
