<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('user_consent_agreements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('consent_agreement_id')->constrained('consent_agreements');
            $table->longText('ip_address');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
