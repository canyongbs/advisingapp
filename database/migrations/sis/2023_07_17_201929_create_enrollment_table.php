<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('sis')
            ->create('enrollment', function (Blueprint $table) {
                $table->integer('enrollment_id');
                $table->string('status');
            });
    }
};
