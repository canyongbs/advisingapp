<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Form\Events\FormSubmissionCreated;
use AdvisingApp\Form\Listeners\NotifySubscribersOfFormSubmission;
use AdvisingApp\Form\Models\FormSubmission;
use AdvisingApp\Form\Notifications\AuthorLinkedFormSubmissionCreatedNotification;
use App\Models\User;
use Illuminate\Support\Facades\Event;

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
