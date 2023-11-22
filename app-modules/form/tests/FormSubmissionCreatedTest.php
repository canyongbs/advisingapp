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

use App\Models\User;
use Illuminate\Support\Facades\Event;
use Assist\Form\Models\FormSubmission;
use Assist\Form\Events\FormSubmissionCreated;
use Assist\Form\Listeners\NotifySubscribersOfFormSubmission;
use Assist\Form\Notifications\AuthorLinkedFormSubmissionCreatedNotification;

it('dispatches FormSubmissionCreated Event when a FormSubmission is created', function () {
    Event::fake(FormSubmissionCreated::class);

    $submission = FormSubmission::factory()->create();

    Event::assertDispatched(
        event: FormSubmissionCreated::class,
        callback: fn (FormSubmissionCreated $event) => $event->submission->is($submission)
    );
});

test('FormSubmissionCreated Event has the proper listeners', function () {
    Event::fake();

    Event::assertListening(
        expectedEvent: FormSubmissionCreated::class,
        expectedListener: NotifySubscribersOfFormSubmission::class
    );
});

test('NotifySubscribersOfFormSubmission dispatches the correct Notification', function () {
    Notification::fake();

    /** @var FormSubmission $submission */
    $submission = FormSubmission::factory()->make();

    $user = User::factory()->create();

    $user->subscriptions()->create([
        'subscribable_id' => $submission->author->getKey(),
        'subscribable_type' => $submission->author->getMorphClass(),
    ]);

    $submission->save();

    $submission->author->subscriptions->map(fn ($subscription) => $subscription->user)->each(function (User $user) {
        Notification::assertSentTo(notifiable: $user, notification: AuthorLinkedFormSubmissionCreatedNotification::class);
    });
});
