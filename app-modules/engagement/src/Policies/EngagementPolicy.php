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
use Assist\Engagement\Models\Engagement;

class EngagementPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'engagement.view-any',
            denyResponse: 'You do not have permission to view engagements.'
        );
    }

    public function view(User $user, Engagement $engagement): Response
    {
        return $user->canOrElse(
            abilities: ['engagement.*.view', "engagement.{$engagement->id}.view"],
            denyResponse: 'You do not have permission to view this engagement.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'engagement.create',
            denyResponse: 'You do not have permission to create engagements.'
        );
    }

    public function update(User $user, Engagement $engagement): Response
    {
        if ($engagement->hasBeenDelivered()) {
            return Response::deny('You do not have permission to update this engagement because it has already been delivered.');
        }

        return $user->canOrElse(
            abilities: ['engagement.*.update', "engagement.{$engagement->id}.update"],
            denyResponse: 'You do not have permission to update this engagement.'
        );
    }

    public function delete(User $user, Engagement $engagement): Response
    {
        if ($engagement->hasBeenDelivered()) {
            return Response::deny('You do not have permission to delete this engagement because it has already been delivered.');
        }

        return $user->canOrElse(
            abilities: ['engagement.*.delete', "engagement.{$engagement->id}.delete"],
            denyResponse: 'You do not have permission to delete this engagement.'
        );
    }

    public function restore(User $user, Engagement $engagement): Response
    {
        return $user->canOrElse(
            abilities: ['engagement.*.restore', "engagement.{$engagement->id}.restore"],
            denyResponse: 'You do not have permission to restore this engagement.'
        );
    }

    public function forceDelete(User $user, Engagement $engagement): Response
    {
        if ($engagement->hasBeenDelivered()) {
            return Response::deny('You cannot permanently delete this engagement because it has already been delivered.');
        }

        return $user->canOrElse(
            abilities: ['engagement.*.force-delete', "engagement.{$engagement->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this engagement.'
        );
    }
}
