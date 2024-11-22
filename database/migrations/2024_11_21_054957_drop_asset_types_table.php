<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('asset_types');
    }

    public function down(): void
    {
        Schema::create('asset_types', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
