<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Engagement\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Engagement\Models\EngagementFile;

class EngagementFilePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'engagement_file.view-any',
            denyResponse: 'You do not have permissions to view engagement files.'
        );
    }

    public function view(User $user, EngagementFile $engagementFile): Response
    {
        return $user->canOrElse(
            abilities: ['engagement_file.*.view', "engagement_file.{$engagementFile->id}.view"],
            denyResponse: 'You do not have permissions to view this engagement file.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'engagement_file.create',
            denyResponse: 'You do not have permissions to create engagement files.'
        );
    }

    public function update(User $user, EngagementFile $engagementFile): Response
    {
        return $user->canOrElse(
            abilities: ['engagement_file.*.update', "engagement_file.{$engagementFile->id}.update"],
            denyResponse: 'You do not have permissions to update this engagement file.'
        );
    }

    public function delete(User $user, EngagementFile $engagementFile): Response
    {
        return $user->canOrElse(
            abilities: ['engagement_file.*.delete', "engagement_file.{$engagementFile->id}.delete"],
            denyResponse: 'You do not have permissions to delete this engagement file.'
        );
    }

    public function restore(User $user, EngagementFile $engagementFile): Response
    {
        return $user->canOrElse(
            abilities: ['engagement_file.*.restore', "engagement_file.{$engagementFile->id}.restore"],
            denyResponse: 'You do not have permissions to restore this engagement file.'
        );
    }

    public function forceDelete(User $user, EngagementFile $engagementFile): Response
    {
        return $user->canOrElse(
            abilities: ['engagement_file.*.force-delete', "engagement_file.{$engagementFile->id}.force-delete"],
            denyResponse: 'You do not have permissions to force delete this engagement file.'
        );
    }
}
