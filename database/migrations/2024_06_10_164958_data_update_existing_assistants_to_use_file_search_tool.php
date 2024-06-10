<?php

use AdvisingApp\Ai\Models\AiAssistant;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $assistants = AiAssistant::query()
            ->get();

        foreach ($assistants as $assistant) {
            $assistant->model->getService()->updateAssistantTools(
                assistant: $assistant,
                tools: ['file_search']
            );

            $assistant->model->getService()->retrieveAssistant($assistant);
        }
    }

    public function down(): void
    {
        $assistants = AiAssistant::query()
            ->get();

        foreach ($assistants as $assistant) {
            $assistant->model->getService()->updateAssistantTools(
                assistant: $assistant,
                tools: []
            );

            $assistant->model->getService()->retrieveAssistant($assistant);
        }
    }
};
