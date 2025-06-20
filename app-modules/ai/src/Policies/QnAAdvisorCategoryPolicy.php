<?php

namespace AdvisingApp\Ai\Policies;

use AdvisingApp\Authorization\Enums\LicenseType;
use App\Concerns\PerformsLicenseChecks;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class QnAAdvisorCategoryPolicy
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
            abilities: 'qna_advisor_category.view-any',
            denyResponse: 'You do not have permission to view QnA Advisor Categories.'
        );
    }

    public function view(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['qna_advisor_category.*.view'],
            denyResponse: 'You do not have permission to view this QnA Advisor Category.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'qna_advisor_category.create',
            denyResponse: 'You do not have permission to create QnA Advisor Categories.'
        );
    }

    public function update(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['qna_advisor_category.*.update'],
            denyResponse: 'You do not have permission to update this QnA Advisor Category.'
        );
    }

    public function delete(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['qna_advisor_category.*.delete'],
            denyResponse: 'You do not have permission to delete this QnA Advisor Categories.'
        );
    }

    public function restore(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['qna_advisor_category.*.restore'],
            denyResponse: 'You do not have permission to restore this QnA Advisor Categories.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['qna_advisor_category.*.force-delete'],
            denyResponse: 'You do not have permission to force-delete this QnA Advisor Categories.'
        );
    }
}
