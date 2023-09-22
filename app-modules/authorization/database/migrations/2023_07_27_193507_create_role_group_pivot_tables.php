<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('role_group_user', function (Blueprint $table) {
            $table->foreignUuid('user_id')->references('id')->on('users');
            $table->foreignUuid('role_group_id')->references('id')->on('role_groups');
            $table->timestamps();
        });

        Schema::create('role_role_group', function (Blueprint $table) {
            $table->foreignUuid('role_id')->references('id')->on('roles');
            $table->foreignUuid('role_group_id')->references('id')->on('role_groups');
            $table->timestamps();
        });
    }
};
