<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('confidential_prompt_users', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('prompt_id')
                ->constrained('prompts');

            $table->foreignUuid('user_id')
                ->constrained('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('confidential_prompt_users');
    }
};
