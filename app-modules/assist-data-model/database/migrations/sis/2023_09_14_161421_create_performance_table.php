<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('performance', function (Blueprint $table) {
            $table->string('sisid');
            $table->string('acad_career');
            $table->string('division');
            $table->boolean('first_gen');
            $table->unsignedInteger('cum_att');
            $table->unsignedInteger('cum_ern');
            $table->unsignedInteger('pct_ern');
            $table->decimal('cum_gpa', 4, 3);
            $table->timestampTz('max_dt');
        });
    }
};
