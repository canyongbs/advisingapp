<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('interaction_campaigns', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};
