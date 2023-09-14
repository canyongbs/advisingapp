<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::connection('sis')
            ->create('programs', function (Blueprint $table) {
                $table->string('sisid');
                $table->string('otherid');
                $table->string('acad_career');
                $table->string('division');
                $table->string('acad_plan');
                $table->string('prog_status');
                $table->decimal('cum_gpa', 4, 3);
                $table->string('semester');
                $table->string('descr');
                $table->string('foi');
                $table->timestampTz('change_dt');
                $table->timestampTz('declare_dt');
            });
    }
};
