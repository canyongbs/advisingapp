<?php

use Laravel\Pennant\Feature;
use App\Features\CustomAiAssistants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('ai_assistants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('assistant_id')->nullable();

            $table->string('profile_image');
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

        Feature::activate(CustomAiAssistants::class);
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_assistants');

        Schema::table('assistant_chats', function (Blueprint $table) {
            $table->dropColumn('ai_assistant_id');
        });
    }
};
