<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('outbound_deliverables', function (Blueprint $table) {
            $table->string('delivery_status')->default('processing')->change();
        });
    }

    public function down(): void
    {
        Schema::table('processing', function (Blueprint $table) {
            $table->string('delivery_status')->default('awaiting')->change();
        });
    }
};
