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
                // TODO: Should sms_opt_out be a boolean?
                $table->string('sms_opt_out')->nullable();
                // TODO: Should email_bounce be a boolean?
                $table->string('email_bounce')->nullable();
                $table->string('phone')->nullable();
                $table->string('address')->nullable();
                $table->string('address_2')->nullable();
                $table->date('birthdate')->nullable();
                // TODO: Is this the year they graduated?
                $table->integer('hsgrad')->nullable();
                // TODO: Should dual be a boolean?
                $table->string('dual')->nullable();
                // TODO: Should ferpa be a boolean?
                $table->string('ferpa')->nullable();
                $table->float('gpa', 4, 3)->nullable();
                $table->string('dfw')->nullable();
                // TODO: Should firstgen be a boolean?
                $table->string('firstgen')->nullable();
                $table->string('ethnicity')->nullable();
                $table->datetime('lastlmslogin')->nullable();
                $table->timestamps();
            });
    }
};
