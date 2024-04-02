<?php

namespace AdvisingApp\Assistant\Actions;

use AdvisingApp\IntegrationAI\Settings\AISettings;

class GetAiAssistant
{
    public function get(): string
    {
        return resolve(AISettings::class)->assistant_id ?? resolve(CreateAiAssistant::class)->create();
    }
}
