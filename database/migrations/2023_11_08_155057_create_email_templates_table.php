<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->string('primary_color')->nullable();

            $table->uuidMorphs('related_to');

            $table->timestamps();

            $table->unique(['name', 'related_to_type', 'related_to_id']);
        });
    }
};
