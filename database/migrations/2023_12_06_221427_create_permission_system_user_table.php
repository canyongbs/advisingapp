<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('permission_system_user', function (Blueprint $table) {
            $table->foreignUuid('system_user_id')->references('id')->on('system_users');
            $table->foreignUuid('permission_id')->references('id')->on('permissions');
            $table->timestamps();
        });
    }
};
