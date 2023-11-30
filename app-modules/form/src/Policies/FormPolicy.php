<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

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
