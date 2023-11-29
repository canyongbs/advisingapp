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

namespace Assist\Application\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Application\Models\Application;

class ApplicationPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'application.view-any',
            denyResponse: 'You do not have permission to view applications.'
        );
    }

    public function view(User $user, Application $application): Response
    {
        return $user->canOrElse(
            abilities: ['application.*.view', "application.{$application->id}.view"],
            denyResponse: 'You do not have permission to view this application.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'application.create',
            denyResponse: 'You do not have permission to create applications.'
        );
    }

    public function update(User $user, Application $application): Response
    {
        return $user->canOrElse(
            abilities: ['application.*.update', "application.{$application->id}.update"],
            denyResponse: 'You do not have permission to update this application.'
        );
    }

    public function delete(User $user, Application $application): Response
    {
        return $user->canOrElse(
            abilities: ['application.*.delete', "application.{$application->id}.delete"],
            denyResponse: 'You do not have permission to delete this application.'
        );
    }

    public function restore(User $user, Application $application): Response
    {
        return $user->canOrElse(
            abilities: ['application.*.restore', "application.{$application->id}.restore"],
            denyResponse: 'You do not have permission to restore this application.'
        );
    }

    public function forceDelete(User $user, Application $application): Response
    {
        return $user->canOrElse(
            abilities: ['application.*.force-delete', "application.{$application->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this application.'
        );
    }
}
