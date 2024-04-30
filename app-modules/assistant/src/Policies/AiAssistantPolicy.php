<?php

namespace AdvisingApp\Assistant\Policies;

use App\Enums\Feature;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use AdvisingApp\Assistant\Models\AiAssistant;

class AiAssistantPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        if (! Gate::check(Feature::CustomAiAssistants->getGateName())) {
            return Response::deny('AI Assistants are not enabled.');
        }

        return $authenticatable->canOrElse(
            abilities: 'ai_assistant.view-any',
            denyResponse: 'You do not have permission to view AI Assistants.'
        );
    }

    public function view(Authenticatable $authenticatable, AiAssistant $aiAssistant): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['ai_assistant.*.view', "ai_assistant.{$aiAssistant->id}.view"],
            denyResponse: 'You do not have permission to view this AI Assistant.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'ai_assistant.create',
            denyResponse: 'You do not have permission to create AI Assistants.'
        );
    }

    public function update(Authenticatable $authenticatable, AiAssistant $aiAssistant): Response
    {
        if ($aiAssistant->assistantChats->isNotEmpty()) {
            return Response::deny('This AI Assistant cannot be edited.');
        }

        return $authenticatable->canOrElse(
            abilities: ['ai_assistant.*.update', "ai_assistant.{$aiAssistant->id}.update"],
            denyResponse: 'You do not have permission to update this AI Assistant.'
        );
    }

    public function delete(Authenticatable $authenticatable, AiAssistant $aiAssistant): Response
    {
        return Response::deny('AI Assistants cannot be deleted.');
    }

    public function restore(Authenticatable $authenticatable, AiAssistant $aiAssistant): Response
    {
        return Response::deny('AI Assistants cannot be restored.');
    }

    public function forceDelete(Authenticatable $authenticatable, AiAssistant $aiAssistant): Response
    {
        return Response::deny('AI Assistants cannot be permanently deleted.');
    }
}
