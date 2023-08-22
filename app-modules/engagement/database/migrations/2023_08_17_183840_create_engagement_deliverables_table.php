<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('engagement_deliverables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('engagement_id')->constrained('engagements');
            $table->string('channel');
            $table->boolean('sent')->nullable();
            $table->longText('response')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
