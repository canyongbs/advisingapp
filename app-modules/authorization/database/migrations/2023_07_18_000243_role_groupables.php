<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('role_groupables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_group_id');
            $table->morphs('role_groupable');
        });
    }
};
