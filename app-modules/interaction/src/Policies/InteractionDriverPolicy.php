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

namespace Assist\Interaction\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Interaction\Models\InteractionDriver;

class InteractionDriverPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction_driver.view-any',
            denyResponse: 'You do not have permission to view interaction drivers.'
        );
    }

    public function view(User $user, InteractionDriver $driver): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_driver.*.view', "interaction_driver.{$driver->id}.view"],
            denyResponse: 'You do not have permission to view this interaction driver.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction_driver.create',
            denyResponse: 'You do not have permission to create interaction drivers.'
        );
    }

    public function update(User $user, InteractionDriver $driver): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_driver.*.update', "interaction_driver.{$driver->id}.update"],
            denyResponse: 'You do not have permission to update this interaction driver.'
        );
    }

    public function delete(User $user, InteractionDriver $driver): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_driver.*.delete', "interaction_driver.{$driver->id}.delete"],
            denyResponse: 'You do not have permission to delete this interaction driver.'
        );
    }

    public function restore(User $user, InteractionDriver $driver): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_driver.*.restore', "interaction_driver.{$driver->id}.restore"],
            denyResponse: 'You do not have permission to restore this interaction driver.'
        );
    }

    public function forceDelete(User $user, InteractionDriver $driver): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_driver.*.force-delete', "interaction_driver.{$driver->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this interaction driver.'
        );
    }
}
