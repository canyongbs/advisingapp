<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.
    - Test

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\KnowledgeBase\Policies;

use App\Enums\Feature;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use App\Concerns\PerformsFeatureChecks;
use App\Concerns\PerformsLicenseChecks;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use App\Policies\Contracts\PerformsChecksBeforeAuthorization;

class KnowledgeBaseItemPolicy implements PerformsChecksBeforeAuthorization
{
    use PerformsLicenseChecks;
    use PerformsFeatureChecks;

    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! is_null($response = $this->hasAnyLicense($authenticatable, [LicenseType::RetentionCrm, LicenseType::RecruitmentCrm]))) {
            return $response;
        }

        if (! is_null($response = $this->hasFeatures())) {
            return $response;
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'knowledge_base_item.view-any',
            denyResponse: 'You do not have permissions to view knowledge base items.'
        );
    }

    public function view(Authenticatable $authenticatable, KnowledgeBaseItem $knowledgeBaseItem): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['knowledge_base_item.*.view', "knowledge_base_item.{$knowledgeBaseItem->id}.view"],
            denyResponse: 'You do not have permissions to view this knowledge base item.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'knowledge_base_item.create',
            denyResponse: 'You do not have permissions to create knowledge base items.'
        );
    }

    public function update(Authenticatable $authenticatable, KnowledgeBaseItem $knowledgeBaseItem): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['knowledge_base_item.*.update', "knowledge_base_item.{$knowledgeBaseItem->id}.update"],
            denyResponse: 'You do not have permissions to update this knowledge base item.'
        );
    }

    public function delete(Authenticatable $authenticatable, KnowledgeBaseItem $knowledgeBaseItem): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['knowledge_base_item.*.delete', "knowledge_base_item.{$knowledgeBaseItem->id}.delete"],
            denyResponse: 'You do not have permissions to delete this knowledge base item.'
        );
    }

    public function restore(Authenticatable $authenticatable, KnowledgeBaseItem $knowledgeBaseItem): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['knowledge_base_item.*.restore', "knowledge_base_item.{$knowledgeBaseItem->id}.restore"],
            denyResponse: 'You do not have permissions to restore this knowledge base item.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, KnowledgeBaseItem $knowledgeBaseItem): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['knowledge_base_item.*.force-delete', "knowledge_base_item.{$knowledgeBaseItem->id}.force-delete"],
            denyResponse: 'You do not have permissions to force delete this knowledge base item.'
        );
    }

    protected function requiredFeatures(): array
    {
        return [Feature::KnowledgeManagement];
    }
}
