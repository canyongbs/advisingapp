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

namespace Assist\Prospect\Policies;

use App\Models\User;
use Assist\Prospect\Models\Prospect;
use Illuminate\Auth\Access\Response;

class ProspectPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'prospect.view-any',
            denyResponse: 'You do not have permission to view prospects.'
        );
    }

    public function view(User $user, Prospect $prospect): Response
    {
        return $user->canOrElse(
            abilities: ['prospect.*.view', "prospect.{$prospect->id}.view"],
            denyResponse: 'You do not have permission to view this prospect.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'prospect.create',
            denyResponse: 'You do not have permission to create prospects.'
        );
    }

    public function import(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'prospect.import',
            denyResponse: 'You do not have permission to import prospects.',
        );
    }

    public function update(User $user, Prospect $prospect): Response
    {
        return $user->canOrElse(
            abilities: ['prospect.*.update', "prospect.{$prospect->id}.update"],
            denyResponse: 'You do not have permission to update this prospect.'
        );
    }

    public function delete(User $user, Prospect $prospect): Response
    {
        return $user->canOrElse(
            abilities: ['prospect.*.delete', "prospect.{$prospect->id}.delete"],
            denyResponse: 'You do not have permission to delete this prospect.'
        );
    }

    public function restore(User $user, Prospect $prospect): Response
    {
        return $user->canOrElse(
            abilities: ['prospect.*.restore', "prospect.{$prospect->id}.restore"],
            denyResponse: 'You do not have permission to restore this prospect.'
        );
    }

    public function forceDelete(User $user, Prospect $prospect): Response
    {
        return $user->canOrElse(
            abilities: ['prospect.*.force-delete', "prospect.{$prospect->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this prospect.'
        );
    }
}
