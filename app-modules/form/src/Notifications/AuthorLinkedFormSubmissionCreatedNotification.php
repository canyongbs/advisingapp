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

namespace Assist\Form\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Filament\Facades\Filament;
use Illuminate\Support\HtmlString;
use Assist\Form\Models\FormSubmission;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Filament\Notifications\Notification as FilamentNotification;

class AuthorLinkedFormSubmissionCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public FormSubmission $submission) {}

    public function via(User $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(User $notifiable): array
    {
        $author = $this->submission->author;

        $name = $author->{$author->displayNameKey()};

        $target = resolve(Filament::getModelResource($author));

        $formSubmissionUrl = $target::getUrl('manage-form-submissions', ['record' => $author]);

        $formSubmissionLink = new HtmlString("<a href='{$formSubmissionUrl}' target='_blank' class='underline'>form submission</a>");

        $morph = str($author->getMorphClass());

        $morphUrl = $target::getUrl('view', ['record' => $author]);

        $morphLink = new HtmlString("<a href='{$morphUrl}' target='_blank' class='underline'>{$name}</a>");

        return FilamentNotification::make()
            ->warning()
            ->title("A {$formSubmissionLink} has been submitted by {$morph} {$morphLink}")
            ->getDatabaseMessage();
    }
}
