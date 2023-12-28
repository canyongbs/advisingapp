<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('asset_exchanges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('asset_id')->constrained('assets');
            $table->string('type');
            $table->string('performed_by_type')->nullable();
            $table->string('performed_by_id')->nullable();
            $table->string('for_type');
            $table->string('for_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
