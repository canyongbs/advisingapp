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

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Engagement\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Engagement\Models\EngagementFile;

class EngagementFilePolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'engagement_file.view-any',
            denyResponse: 'You do not have permissions to view engagement files.'
        );
    }

    public function view(Authenticatable $authenticatable, EngagementFile $engagementFile): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["engagement_file.{$engagementFile->id}.view"],
            denyResponse: 'You do not have permissions to view this engagement file.'
        );
    }

    public function create(Authenticatable $authenticatable, ?Prospect $prospect = null): Response
    {
        if ($prospect && $prospect->student_id) {
            return Response::deny('You cannot create engagement file as Prospect has been converted to a Student.');
        }

        return $authenticatable->canOrElse(
            abilities: 'engagement_file.create',
            denyResponse: 'You do not have permissions to create engagement files.'
        );
    }

    public function update(Authenticatable $authenticatable, EngagementFile $engagementFile): Response
    {
        if (count($engagementFile->prospects) && $engagementFile->prospects->first()->student_id) {
            return Response::deny('You cannot edit engagement file as Prospect has been converted to a Student.');
        }

        return $authenticatable->canOrElse(
            abilities: ["engagement_file.{$engagementFile->id}.update"],
            denyResponse: 'You do not have permissions to update this engagement file.'
        );
    }

    public function delete(Authenticatable $authenticatable, EngagementFile $engagementFile): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["engagement_file.{$engagementFile->id}.delete"],
            denyResponse: 'You do not have permissions to delete this engagement file.'
        );
    }

    public function restore(Authenticatable $authenticatable, EngagementFile $engagementFile): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["engagement_file.{$engagementFile->id}.restore"],
            denyResponse: 'You do not have permissions to restore this engagement file.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, EngagementFile $engagementFile): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["engagement_file.{$engagementFile->id}.force-delete"],
            denyResponse: 'You do not have permissions to force delete this engagement file.'
        );
    }
}
