<?php

namespace AdvisingApp\Assistant\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use AdvisingApp\IntegrationAI\Settings\AISettings;
use AdvisingApp\Assistant\Actions\UpdateAiAssistant;
use AdvisingApp\Assistant\DataTransferObjects\AiAssistantUpdateData;
use AdvisingApp\Assistant\Actions\CreateDefaultInstitutionAiAssistant;

class UpdateAiAssistantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $assistantId,
        public AiAssistantUpdateData $data,
    ) {}

    public function handle(): void
    {
        resolve(UpdateAiAssistant::class)->from(
            assistantId: $this->assistantId,
            data: $this->data
        );
    }
}
