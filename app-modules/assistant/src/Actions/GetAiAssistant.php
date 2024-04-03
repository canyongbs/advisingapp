<?php

namespace AdvisingApp\Assistant\Actions;

use AdvisingApp\IntegrationAI\Settings\AISettings;

class GetAiAssistant
{
    public function get(): string
    {
        // We do this so that we create an AI Assistant if it doesn't already exist
        // This will be triggered when the first user for an organization loads up the Personal Assistant
        ray('assistant', resolve(AISettings::class)->assistant_id);

        return resolve(AISettings::class)->assistant_id ?? resolve(CreateAiAssistant::class)->create();
    }
}
