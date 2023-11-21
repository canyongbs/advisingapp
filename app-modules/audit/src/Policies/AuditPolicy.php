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

namespace Assist\Audit\Policies;

use App\Models\User;
use Assist\Audit\Models\Audit;
use Illuminate\Auth\Access\Response;

class AuditPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'audit.view-any',
            denyResponse: 'You do not have permission to view audits.'
        );
    }

    public function view(User $user, Audit $audit): Response
    {
        return $user->canOrElse(
            abilities: ['audit.*.view', "audit.{$audit->id}.view"],
            denyResponse: 'You do not have permission to view this audit.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'audit.create',
            denyResponse: 'You do not have permission to create audits.'
        );
    }

    public function update(User $user, Audit $audit): Response
    {
        return $user->canOrElse(
            abilities: ['audit.*.update', "audit.{$audit->id}.update"],
            denyResponse: 'You do not have permission to update this audit.'
        );
    }

    public function delete(User $user, Audit $audit): Response
    {
        return $user->canOrElse(
            abilities: ['audit.*.delete', "audit.{$audit->id}.delete"],
            denyResponse: 'You do not have permission to delete this audit.'
        );
    }

    public function restore(User $user, Audit $audit): Response
    {
        return $user->canOrElse(
            abilities: ['audit.*.restore', "audit.{$audit->id}.restore"],
            denyResponse: 'You do not have permission to restore this audit.'
        );
    }

    public function forceDelete(User $user, Audit $audit): Response
    {
        return $user->canOrElse(
            abilities: ['audit.*.force-delete', "audit.{$audit->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this audit.'
        );
    }
}
