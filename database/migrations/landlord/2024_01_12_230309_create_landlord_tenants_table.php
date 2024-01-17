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
            $table->text('key')->nullable();
            $table->text('db_host')->nullable();
            $table->text('db_port')->nullable();
            $table->text('database')->nullable();
            $table->text('db_username')->nullable();
            $table->text('db_password')->nullable();
            $table->text('sis_db_host')->nullable();
            $table->text('sis_db_port')->nullable();
            $table->text('sis_database')->nullable();
            $table->text('sis_db_username')->nullable();
            $table->text('sis_db_password')->nullable();
            $table->timestamps();

            $table->unique(['db_host', 'db_port', 'database', 'db_username', 'db_password']);
            $table->unique(['sis_db_host', 'sis_db_port', 'sis_database', 'sis_db_username', 'sis_db_password']);
        });
    }
};
