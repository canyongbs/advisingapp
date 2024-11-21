<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('asset_statuses');
    }

    public function down(): void {
        Schema::create('asset_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('classification');
            $table->string('name');

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
