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

namespace Assist\Division\Policies;

use App\Models\User;
use Assist\Division\Models\Division;
use Illuminate\Auth\Access\Response;

class DivisionPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'division.view-any',
            denyResponse: 'You do not have permission to view divisions.'
        );
    }

    public function view(User $user, Division $division): Response
    {
        return $user->canOrElse(
            abilities: ['division.*.view', "division.{$division->id}.view"],
            denyResponse: 'You do not have permission to view this division.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'division.create',
            denyResponse: 'You do not have permission to create divisions.'
        );
    }

    public function update(User $user, Division $division): Response
    {
        return $user->canOrElse(
            abilities: ['division.*.update', "division.{$division->id}.update"],
            denyResponse: 'You do not have permission to update this division.'
        );
    }

    public function delete(User $user, Division $division): Response
    {
        return $user->canOrElse(
            abilities: ['division.*.delete', "division.{$division->id}.delete"],
            denyResponse: 'You do not have permission to delete this division.'
        );
    }

    public function restore(User $user, Division $division): Response
    {
        return $user->canOrElse(
            abilities: ['division.*.restore', "division.{$division->id}.restore"],
            denyResponse: 'You do not have permission to restore this division.'
        );
    }

    public function forceDelete(User $user, Division $division): Response
    {
        return $user->canOrElse(
            abilities: ['division.*.force-delete', "division.{$division->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this division.'
        );
    }
}
