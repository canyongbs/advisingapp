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
            $table->string('db_host');
            $table->string('db_port');
            $table->string('database');
            $table->string('db_username');
            $table->string('db_password');
            $table->string('sis_db_host');
            $table->string('sis_db_port');
            $table->string('sis_database');
            $table->string('sis_db_username');
            $table->string('sis_db_password');
            $table->timestamps();
        });
    }
};
