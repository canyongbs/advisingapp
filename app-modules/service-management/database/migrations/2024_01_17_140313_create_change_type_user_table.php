<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('change_request_type_user', function (Blueprint $table) {
            $table->foreignUuid('change_request_type_id')->constrained('change_request_types');
            $table->foreignUuid('user_id')->constrained('users');
        });
    }
};
