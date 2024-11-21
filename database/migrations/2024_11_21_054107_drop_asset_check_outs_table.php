<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('asset_check_outs');
    }

    public function down(): void
    {
        Schema::create('asset_check_outs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('asset_id')->constrained('assets');
            $table->foreignUuid('asset_check_in_id')->nullable()->constrained('asset_check_ins');
            $table->string('checked_out_by_type')->nullable();
            $table->string('checked_out_by_id')->nullable();
            $table->string('checked_out_to_type');
            $table->string('checked_out_to_id');
            $table->timestamp('checked_out_at');
            $table->timestamp('expected_check_in_at')->nullable();
            $table->longText('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
