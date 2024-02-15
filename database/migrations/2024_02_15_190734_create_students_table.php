<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->string('sisid')->primary();
            $table->string('otherid')->nullable();
            $table->string('first')->nullable();
            $table->string('last')->nullable();
            $table->string('full_name')->nullable();
            $table->string('preferred')->nullable();
            $table->string('email')->nullable();
            $table->string('email_2')->nullable();
            $table->string('mobile')->nullable();
            $table->boolean('sms_opt_out')->default(false);
            $table->boolean('email_bounce')->default(false);
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('address2')->nullable();
            $table->string('address3')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal')->nullable();
            $table->date('birthdate')->nullable();
            $table->integer('hsgrad')->nullable();
            $table->boolean('dual')->default(false);
            $table->boolean('ferpa')->default(false);
            $table->date('dfw')->nullable();
            $table->boolean('sap')->default(false);
            $table->string('holds')->nullable();
            $table->boolean('firstgen')->default(false);
            $table->string('ethnicity')->nullable();
            $table->timestamp('lastlmslogin')->nullable();
            $table->string('f_e_term')->nullable();
            $table->string('mr_e_term')->nullable();
        });
    }
};
