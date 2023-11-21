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
use Assist\Interaction\Models\InteractionOutcome;

class InteractionOutcomePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction_outcome.view-any',
            denyResponse: 'You do not have permission to view interaction outcomes.'
        );
    }

    public function view(User $user, InteractionOutcome $outcome): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_outcome.*.view', "interaction_outcome.{$outcome->id}.view"],
            denyResponse: 'You do not have permission to view this interaction outcome.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction_outcome.create',
            denyResponse: 'You do not have permission to create interaction outcomes.'
        );
    }

    public function update(User $user, InteractionOutcome $outcome): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_outcome.*.update', "interaction_outcome.{$outcome->id}.update"],
            denyResponse: 'You do not have permission to update this interaction outcome.'
        );
    }

    public function delete(User $user, InteractionOutcome $outcome): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_outcome.*.delete', "interaction_outcome.{$outcome->id}.delete"],
            denyResponse: 'You do not have permission to delete this interaction outcome.'
        );
    }

    public function restore(User $user, InteractionOutcome $outcome): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_outcome.*.restore', "interaction_outcome.{$outcome->id}.restore"],
            denyResponse: 'You do not have permission to restore this interaction outcome.'
        );
    }

    public function forceDelete(User $user, InteractionOutcome $outcome): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_outcome.*.force-delete', "interaction_outcome.{$outcome->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this interaction outcome.'
        );
    }
}
