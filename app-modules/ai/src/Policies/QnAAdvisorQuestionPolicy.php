<?php

namespace AdvisingApp\Ai\Policies;

use AdvisingApp\Authorization\Enums\LicenseType;
use App\Concerns\PerformsLicenseChecks;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class QnAAdvisorQuestionPolicy
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
            abilities: 'qna_advisor_question.view-any',
            denyResponse: 'You do not have permission to view QnA Advisor Questions.'
        );
    }

    public function view(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['qna_advisor_question.*.view'],
            denyResponse: 'You do not have permission to view this QnA Advisor Question.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'qna_advisor_question.create',
            denyResponse: 'You do not have permission to create QnA Advisor Questions.'
        );
    }

    public function update(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['qna_advisor_question.*.update'],
            denyResponse: 'You do not have permission to update this QnA Advisor Question.'
        );
    }

    public function delete(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['qna_advisor_question.*.delete'],
            denyResponse: 'You do not have permission to delete this QnA Advisor Question.'
        );
    }

    public function restore(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['qna_advisor_question.*.restore'],
            denyResponse: 'You do not have permission to restore this QnA Advisor Question.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['qna_advisor_question.*.force-delete'],
            denyResponse: 'You do not have permission to force-delete this QnA Advisor Question.'
        );
    }
}
