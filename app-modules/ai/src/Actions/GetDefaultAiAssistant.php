<?php

namespace AdvisingApp\Ai\Actions;

use App\Models\Tenant;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Enums\AiApplication;
use AdvisingApp\Ai\Settings\AISettings;

class GetDefaultAiAssistant
{
    public function __invoke(AiApplication $application): AiAssistant
    {
        $assistant = AiAssistant::query()
            ->where('application', $application)
            ->where('is_default', true)
            ->first();

        if ($assistant) {
            return $assistant;
        }

        $tenant = Tenant::current();
        $settings = app(AISettings::class);

        $assistant = new AiAssistant();
        $assistant->name = "{$tenant->name} AI Assistant";
        $assistant->description = "An AI Assistant for {$tenant->name}";
        $assistant->instructions = $settings->prompt_system_context;
        $assistant->application = $application;
        $assistant->model = $settings->getDefaultModel();
        $assistant->is_default = true;

        $assistant->model->getService()->createAssistant($assistant);

        $assistant->save();

        return $assistant;
    }
}
