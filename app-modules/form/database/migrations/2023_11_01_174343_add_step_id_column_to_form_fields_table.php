<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('form_fields', function (Blueprint $table) {
            $table->foreignUuid('step_id')->nullable()->constrained('form_steps')->cascadeOnDelete();
        });
    }
};
