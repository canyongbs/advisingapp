<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('inbound_webhooks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('source');
            $table->string('event');
            $table->longText('url');
            $table->longText('payload');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
