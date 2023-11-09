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

            $table->uuidMorphs('model');

            $table->timestamps();

            $table->unique(['name', 'model_id', 'model_type']);
        });
    }
};
