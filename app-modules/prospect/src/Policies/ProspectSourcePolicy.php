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
use Illuminate\Auth\Access\Response;
use Assist\Prospect\Models\ProspectSource;

class ProspectSourcePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'prospect_source.view-any',
            denyResponse: 'You do not have permission to view prospect sources.'
        );
    }

    public function view(User $user, ProspectSource $prospectSource): Response
    {
        return $user->canOrElse(
            abilities: ['prospect_source.*.view', "prospect_source.{$prospectSource->id}.view"],
            denyResponse: 'You do not have permission to view this prospect source.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'prospect_source.create',
            denyResponse: 'You do not have permission to create prospect sources.'
        );
    }

    public function update(User $user, ProspectSource $prospectSource): Response
    {
        return $user->canOrElse(
            abilities: ['prospect_source.*.update', "prospect_source.{$prospectSource->id}.update"],
            denyResponse: 'You do not have permission to update this prospect source.'
        );
    }

    public function delete(User $user, ProspectSource $prospectSource): Response
    {
        return $user->canOrElse(
            abilities: ['prospect_source.*.delete', "prospect_source.{$prospectSource->id}.delete"],
            denyResponse: 'You do not have permission to delete this prospect source.'
        );
    }

    public function restore(User $user, ProspectSource $prospectSource): Response
    {
        return $user->canOrElse(
            abilities: ['prospect_source.*.restore', "prospect_source.{$prospectSource->id}.restore"],
            denyResponse: 'You do not have permission to restore this prospect source.'
        );
    }

    public function forceDelete(User $user, ProspectSource $prospectSource): Response
    {
        return $user->canOrElse(
            abilities: ['prospect_source.*.force-delete', "prospect_source.{$prospectSource->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this prospect source.'
        );
    }
}
