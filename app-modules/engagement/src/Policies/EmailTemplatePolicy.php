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

namespace Assist\Engagement\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Engagement\Models\EmailTemplate;

class EmailTemplatePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'email_template.view-any',
            denyResponse: 'You do not have permission to view email templates.'
        );
    }

    public function view(User $user, EmailTemplate $emailTemplate): Response
    {
        return $user->canOrElse(
            abilities: ['email_template.*.view', "email_template.{$emailTemplate->id}.view"],
            denyResponse: 'You do not have permission to view this email template.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'email_template.create',
            denyResponse: 'You do not have permission to create email templates.'
        );
    }

    public function update(User $user, EmailTemplate $emailTemplate): Response
    {
        return $user->canOrElse(
            abilities: ['email_template.*.update', "email_template.{$emailTemplate->id}.update"],
            denyResponse: 'You do not have permission to update this email template.'
        );
    }

    public function delete(User $user, EmailTemplate $emailTemplate): Response
    {
        return $user->canOrElse(
            abilities: ['email_template.*.delete', "email_template.{$emailTemplate->id}.delete"],
            denyResponse: 'You do not have permission to delete this email template.'
        );
    }

    public function restore(User $user, EmailTemplate $emailTemplate): Response
    {
        return $user->canOrElse(
            abilities: ['email_template.*.restore', "email_template.{$emailTemplate->id}.restore"],
            denyResponse: 'You do not have permission to restore this email template.'
        );
    }

    public function forceDelete(User $user, EmailTemplate $emailTemplate): Response
    {
        return $user->canOrElse(
            abilities: ['email_template.*.force-delete', "email_template.{$emailTemplate->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this email template.'
        );
    }
}
