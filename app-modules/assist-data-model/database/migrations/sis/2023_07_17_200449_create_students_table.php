<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::connection('sis')
            ->create('students', function (Blueprint $table) {
                $table->string('sisid');
                $table->string('otherid')->nullable();
                $table->string('first')->nullable();
                $table->string('last')->nullable();
                $table->string('full')->nullable();
                $table->string('preferred')->nullable();
                $table->string('email')->nullable();
                $table->string('email_2')->nullable();
                $table->string('mobile')->nullable();
                $table->boolean('sms_opt_out');
                $table->boolean('email_bounce');
                $table->string('phone')->nullable();
                $table->string('address')->nullable();
                $table->string('address_2')->nullable();
                $table->string('address_3')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('postal')->nullable();
                $table->date('birthdate')->nullable();
                $table->integer('hsgrad')->nullable();
                $table->boolean('dual')->nullable();
                $table->boolean('ferpa')->nullable();
                $table->date('dfw')->nullable();
                $table->boolean('sap')->nullable();
                $table->string('holds')->nullable();
                $table->boolean('firstgen')->nullable();
                $table->string('ethnicity')->nullable();
                $table->datetime('lastlmslogin')->nullable();
                $table->string('f_e_term')->nullable();
                $table->string('mr_e_term')->nullable();
            });
    }
};
