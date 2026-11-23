<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('ai_assistant_confidential_user_groups', function (Blueprint $table): void {
            $table->uuid('id')->primary();

            $table->foreignUuid('ai_assistant_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_group_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_assistant_confidential_user_groups');
    }
};
