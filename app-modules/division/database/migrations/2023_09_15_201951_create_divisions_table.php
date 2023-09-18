<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->string('code')->unique(); //unique?
            $table->longText('header')->nullable();
            $table->longText('footer')->nullable();

            $table->foreignUuid('created_by_id')->nullable()->constrained('users');
            $table->foreignUuid('updated_by_id')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
