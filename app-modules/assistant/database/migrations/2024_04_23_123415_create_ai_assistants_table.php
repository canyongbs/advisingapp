<?php

use Laravel\Pennant\Feature;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('ai_assistants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('assistant_id')->nullable();

            $table->string('name');
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->longText('instructions')->nullable();
            $table->longText('knowledge')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('assistant_chats', function (Blueprint $table) {
            $table->foreignUuid('ai_assistant_id')->nullable()->constrained('ai_assistants');
        });

        Feature::activate('custom-ai-assistants');

        Schema::table('assistant_chats', function (Blueprint $table) {
            $table->dropColumn('assistant_id');
        });
    }

    public function down(): void
    {
        Schema::table('assistant_chats', function (Blueprint $table) {
            $table->string('assistant_id')->nullable();
        });

        Feature::purge('custom-ai-assistants');

        Schema::table('assistant_chats', function (Blueprint $table) {
            $table->dropColumn('ai_assistant_id');
        });

        Schema::dropIfExists('ai_assistants');
    }
};
