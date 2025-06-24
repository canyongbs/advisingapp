<?php

namespace AdvisingApp\Ai\Policies;

use AdvisingApp\Authorization\Enums\LicenseType;
use App\Concerns\PerformsLicenseChecks;
use App\Enums\Feature;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

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
        if (! Gate::check(Feature::QnAAdvisor->getGateName())) {
            return Response::deny('QnA Advisors are not enabled.');
        }
        
        return $authenticatable->canOrElse(
            abilities: 'qna_advisor.view-any',
            denyResponse: 'You do not have permission to view QnA Advisors.'
        );
    }

    public function view(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['qna_advisor.*.view'],
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
            abilities: ['qna_advisor.*.update'],
            denyResponse: 'You do not have permission to update this QnA Advisor.'
        );
    }

    public function delete(Authenticatable $authenticatable): Response
    {
        return Response::deny('QnA Advisor cannot be deleted.');
    }

    public function restore(Authenticatable $authenticatable): Response
    {
        return Response::deny('QnA Advisor cannot be restored.');
    }

    public function forceDelete(Authenticatable $authenticatable): Response
    {
        return Response::deny('QnA Advisor cannot be permanently deleted.');
    }
}
