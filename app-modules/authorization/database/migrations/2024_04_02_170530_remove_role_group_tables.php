<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('role_role_group');
        Schema::dropIfExists('role_group_user');
        Schema::dropIfExists('role_groups');
    }

    public function down(): void
    {
        Schema::create('role_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name', 125);
            $table->string('guard_name', 125);
            $table->string('slug', 125)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['name', 'guard_name']);
        });

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
