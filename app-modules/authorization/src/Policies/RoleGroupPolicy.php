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

namespace Assist\Authorization\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Authorization\Models\RoleGroup;

class RoleGroupPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'role_group.view-any',
            denyResponse: 'You do not have permission to view role groups.'
        );
    }

    public function view(User $user, RoleGroup $roleGroup): Response
    {
        return $user->canOrElse(
            abilities: ['role_group.*.view', "role_group.{$roleGroup->id}.view"],
            denyResponse: 'You do not have permission to view this role group.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'role_group.create',
            denyResponse: 'You do not have permission to create role groups.'
        );
    }

    public function update(User $user, RoleGroup $roleGroup): Response
    {
        return $user->canOrElse(
            abilities: ['role_group.*.update', "role_group.{$roleGroup->id}.update"],
            denyResponse: 'You do not have permission to update this role group.'
        );
    }

    public function delete(User $user, RoleGroup $roleGroup): Response
    {
        return $user->canOrElse(
            abilities: ['role_group.*.delete', "role_group.{$roleGroup->id}.delete"],
            denyResponse: 'You do not have permission to delete this role group.'
        );
    }

    public function restore(User $user, RoleGroup $roleGroup): Response
    {
        return $user->canOrElse(
            abilities: ['role_group.*.restore', "role_group.{$roleGroup->id}.restore"],
            denyResponse: 'You do not have permission to restore this role group.'
        );
    }

    public function forceDelete(User $user, RoleGroup $roleGroup): Response
    {
        return $user->canOrElse(
            abilities: ['role_group.*.force-delete', "role_group.{$roleGroup->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this role group.'
        );
    }
}
