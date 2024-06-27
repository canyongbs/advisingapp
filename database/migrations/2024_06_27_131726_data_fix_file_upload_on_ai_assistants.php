<?php

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Models\AiAssistant;
use Illuminate\Database\Migrations\Migration;
use AdvisingApp\IntegrationOpenAi\Models\AzureOpenAiVectorStore;

return new class () extends Migration {
    public function up(): void
    {
        $assistants = AiAssistant::query()
            ->where('model', AiModel::OpenAiGpt4o)
            ->get();

        foreach ($assistants as $assistant) {
            $service = $assistant->model->getService();

            $createdVectorStore = $service->createVectorStore([]);

            $service->enableAssistantFileUploads($assistant, $createdVectorStore->id);

            $vectorStore = new AzureOpenAiVectorStore();
            $vectorStore->vector_store_id = $createdVectorStore->id;
            $vectorStore->vector_storable_id = $assistant->id;
            $vectorStore->vector_storable_type = $assistant->getMorphClass();
            $vectorStore->save();
        }
    }

    public function down(): void {}
};
