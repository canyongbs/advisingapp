<?php

namespace AdvisingApp\Assistant\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use App\Concerns\PerformsLicenseChecks;
use AdvisingApp\Assistant\Models\Prompt;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Policies\Contracts\PerformsChecksBeforeAuthorization;

class PromptPolicy implements PerformsChecksBeforeAuthorization
{
    use PerformsLicenseChecks;

    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! is_null($response = $this->hasLicenses($authenticatable, LicenseType::ConversationalAi))) {
            return $response;
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'prompt.view-any',
            denyResponse: 'You do not have permission to view prompts.'
        );
    }

    public function view(Authenticatable $authenticatable, Prompt $prompt): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prompt.*.view', "prompt.{$prompt->id}.view"],
            denyResponse: 'You do not have permission to view this prompt.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'prompt.create',
            denyResponse: 'You do not have permission to create prompts.'
        );
    }

    public function update(Authenticatable $authenticatable, Prompt $prompt): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prompt.*.update', "prompt.{$prompt->id}.update"],
            denyResponse: 'You do not have permission to update this prompt.'
        );
    }

    public function delete(Authenticatable $authenticatable, Prompt $prompt): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prompt.*.delete', "prompt.{$prompt->id}.delete"],
            denyResponse: 'You do not have permission to delete this prompt.'
        );
    }

    public function restore(Authenticatable $authenticatable, Prompt $prompt): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prompt.*.restore', "prompt.{$prompt->id}.restore"],
            denyResponse: 'You do not have permission to restore this prompt.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, Prompt $prompt): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prompt.*.force-delete', "prompt.{$prompt->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this prompt.'
        );
    }
}
