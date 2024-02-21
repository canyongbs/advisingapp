<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('portal_authentications', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('educatable_id')->nullable();
            $table->string('educatable_type')->nullable();
            $table->string('code')->nullable();
            $table->string('portal_type')->nullable();

            $table->timestamps();

            $table->index(['educatable_type', 'educatable_id']);
        });
    }
};
