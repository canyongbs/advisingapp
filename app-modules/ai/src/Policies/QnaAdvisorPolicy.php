<?php

namespace AdvisingApp\Ai\Policies;

use App\Concerns\PerformsLicenseChecks;
use App\Models\Authenticatable;
use AdvisingApp\Authorization\Enums\LicenseType;
use Illuminate\Auth\Access\Response;

class QnaAdvisorPolicy
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
            abilities: 'qna_advisor.view-any',
            denyResponse: 'You do not have permission to view QnA Advisor.'
        );
    }

    public function view(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["qna_advisor.*.view"],
            denyResponse: 'You do not have permission to view this QnA Advisor.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'qna_advisor.create',
            denyResponse: 'You do not have permission to create QnA Advisors.'
        );
    }

    public function update(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["qna_advisor.*.update"],
            denyResponse: 'You do not have permission to update this QnA Advisor.'
        );
    }

    public function delete(Authenticatable $authenticatable): Response
    {
        return Response::deny('QnA Advisors cannot be deleted.');
    }

    public function restore(Authenticatable $authenticatable): Response
    {
        return Response::deny('QnA Advisors cannot be restored.');
    }

    public function forceDelete(Authenticatable $authenticatable): Response
    {
        return Response::deny('QnA Advisors cannot be permanently deleted.');
    }
}

