<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('confidential_prompt_user_groups', function (Blueprint $table): void {
            $table->uuid('id')->primary();

            $table->foreignUuid('prompt_id')->constrained('prompts')->cascadeOnDelete();
            $table->foreignUuid('user_group_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('confidential_prompt_user_groups');
    }
};
