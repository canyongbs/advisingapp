<?php

namespace AdvisingApp\Assistant\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use App\Concerns\PerformsLicenseChecks;
use AdvisingApp\Assistant\Models\PromptType;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Policies\Contracts\PerformsChecksBeforeAuthorization;

class PromptTypePolicy implements PerformsChecksBeforeAuthorization
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
            abilities: 'prompt_type.view-any',
            denyResponse: 'You do not have permission to view prompt types.'
        );
    }

    public function view(Authenticatable $authenticatable, PromptType $promptType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prompt_type.*.view', "prompt_type.{$promptType->id}.view"],
            denyResponse: 'You do not have permission to view this prompt type.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'prompt_type.create',
            denyResponse: 'You do not have permission to create prompt types.'
        );
    }

    public function update(Authenticatable $authenticatable, PromptType $promptType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prompt_type.*.update', "prompt_type.{$promptType->id}.update"],
            denyResponse: 'You do not have permission to update this prompt type.'
        );
    }

    public function delete(Authenticatable $authenticatable, PromptType $promptType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prompt_type.*.delete', "prompt_type.{$promptType->id}.delete"],
            denyResponse: 'You do not have permission to delete this prompt type.'
        );
    }

    public function restore(Authenticatable $authenticatable, PromptType $promptType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prompt_type.*.restore', "prompt_type.{$promptType->id}.restore"],
            denyResponse: 'You do not have permission to restore this prompt type.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, PromptType $promptType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prompt_type.*.force-delete', "prompt_type.{$promptType->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this prompt type.'
        );
    }
}
