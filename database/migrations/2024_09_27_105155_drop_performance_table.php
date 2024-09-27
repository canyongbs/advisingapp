<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('performance');
    }

    public function down(): void
    {
        Schema::create('performance', function (Blueprint $table) {
            $table->string('sisid');
            $table->string('acad_career');
            $table->string('division');
            $table->boolean('first_gen');
            $table->integer('cum_att');
            $table->integer('cum_ern');
            $table->integer('pct_ern');
            $table->float('cum_gpa', 4, 3);
            $table->timestampTz('max_dt');
        });
    }
};
