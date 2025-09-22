<?php

use App\Features\AiAssistantUseFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::create('ai_assistant_uses', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('assistant_id')->constrained('ai_assistants')->cascadeOnDelete();
                $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });

            DB::statement(<<<'SQL'
                insert into ai_assistant_uses (id, assistant_id, user_id, created_at, updated_at)
                select distinct
                    ai_messages.id,
                    ai_threads.assistant_id,
                    ai_messages.user_id,
                    ai_messages.created_at,
                    ai_messages.updated_at
                from ai_messages
                join ai_threads on ai_messages.thread_id = ai_threads.id
                where ai_messages.user_id is not null
                SQL);

            AiAssistantUseFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            AiAssistantUseFeature::deactivate();

            Schema::dropIfExists('ai_assistant_uses');
        });
    }
};
