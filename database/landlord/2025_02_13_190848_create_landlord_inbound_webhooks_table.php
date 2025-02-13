<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('landlord_inbound_webhooks', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('source');
            $table->string('event');
            $table->longText('url');
            $table->longText('payload');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landlord_inbound_webhooks');
    }
};
