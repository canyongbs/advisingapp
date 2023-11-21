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

namespace Assist\Team\Policies;

use App\Models\User;
use Assist\Team\Models\Team;
use Illuminate\Auth\Access\Response;

class TeamPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'team.view-any',
            denyResponse: 'You do not have permission to view interactions.'
        );
    }

    public function view(User $user, Team $team): Response
    {
        return $user->canOrElse(
            abilities: ['team.*.view', "team.{$team->id}.view"],
            denyResponse: 'You do not have permission to view this team.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'team.create',
            denyResponse: 'You do not have permission to create interactions.'
        );
    }

    public function update(User $user, Team $team): Response
    {
        return $user->canOrElse(
            abilities: ['team.*.update', "team.{$team->id}.update"],
            denyResponse: 'You do not have permission to update this team.'
        );
    }

    public function delete(User $user, Team $team): Response
    {
        return $user->canOrElse(
            abilities: ['team.*.delete', "team.{$team->id}.delete"],
            denyResponse: 'You do not have permission to delete this team.'
        );
    }

    public function restore(User $user, Team $team): Response
    {
        return $user->canOrElse(
            abilities: ['team.*.restore', "team.{$team->id}.restore"],
            denyResponse: 'You do not have permission to restore this team.'
        );
    }

    public function forceDelete(User $user, Team $team): Response
    {
        return $user->canOrElse(
            abilities: ['team.*.force-delete', "team.{$team->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this team.'
        );
    }
}
