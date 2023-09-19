<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('consent_agreement_user', function (Blueprint $table) {
            $table->foreignUuid('consent_agreement_id')->constrained('consent_agreements');
            $table->foreignUuid('user_id')->constrained('users');
            $table->longText('ip_address');
            $table->timestamps();
        });
    }
};
