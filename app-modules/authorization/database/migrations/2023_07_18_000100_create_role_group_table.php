<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('role_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 125);
            $table->string('slug', 125);
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
