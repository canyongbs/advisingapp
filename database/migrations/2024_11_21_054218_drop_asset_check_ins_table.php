<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('asset_check_ins');
    }

    public function down(): void
    {
        Schema::create('asset_check_ins', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('asset_id')->constrained('assets');
            $table->string('checked_in_by_type')->nullable();
            $table->string('checked_in_by_id')->nullable();
            $table->string('checked_in_from_type');
            $table->string('checked_in_from_id');
            $table->timestamp('checked_in_at');
            $table->longText('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
