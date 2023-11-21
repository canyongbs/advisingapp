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

namespace Assist\Form\Policies;

use App\Models\User;
use Assist\Form\Models\Form;
use Illuminate\Auth\Access\Response;

class FormPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'form.view-any',
            denyResponse: 'You do not have permission to view forms.'
        );
    }

    public function view(User $user, Form $form): Response
    {
        return $user->canOrElse(
            abilities: ['form.*.view', "form.{$form->id}.view"],
            denyResponse: 'You do not have permission to view this form.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'form.create',
            denyResponse: 'You do not have permission to create forms.'
        );
    }

    public function update(User $user, Form $form): Response
    {
        return $user->canOrElse(
            abilities: ['form.*.update', "form.{$form->id}.update"],
            denyResponse: 'You do not have permission to update this form.'
        );
    }

    public function delete(User $user, Form $form): Response
    {
        return $user->canOrElse(
            abilities: ['form.*.delete', "form.{$form->id}.delete"],
            denyResponse: 'You do not have permission to delete this form.'
        );
    }

    public function restore(User $user, Form $form): Response
    {
        return $user->canOrElse(
            abilities: ['form.*.restore', "form.{$form->id}.restore"],
            denyResponse: 'You do not have permission to restore this form.'
        );
    }

    public function forceDelete(User $user, Form $form): Response
    {
        return $user->canOrElse(
            abilities: ['form.*.force-delete', "form.{$form->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this form.'
        );
    }
}
