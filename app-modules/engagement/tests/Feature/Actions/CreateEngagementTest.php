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

use AdvisingApp\Engagement\Actions\CreateEngagement;
use AdvisingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Notifications\EngagementNotification;
use AdvisingApp\Engagement\Tests\RequestFactories\CreateEngagementRequestFactory;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\assertDatabaseCount;

it('will create and send an engagement immediately', function () {
    Notification::fake();

    assertDatabaseCount(Engagement::class, 0);

    $data = CreateEngagementRequestFactory::new()->create();

    app(CreateEngagement::class)->execute(new EngagementCreationData(
        user: $data['user'],
        recipient: $data['recipient'],
        channel: $data['channel'],
        subject: $data['subject'],
        body: $data['body'],
        scheduledAt: null,
    ));

    assertDatabaseCount(Engagement::class, 1);

    expect(Engagement::first())
        ->user->is($data['user'])->toBeTrue()
        ->recipient->is($data['recipient'])->toBeTrue()
        ->channel->toBe($data['channel'])
        ->subject->toBe($data['subject'])
        ->body->toMatchArray($data['body'])
        ->scheduled_at->toBeNull()
        ->dispatched_at->not->toBeNull();

    Notification::assertSentTo(
        $data['recipient'],
        EngagementNotification::class
    );
});

it('will create but not dispatch a scheduled engagement', function () {
    Notification::fake();

    assertDatabaseCount(Engagement::class, 0);

    $data = CreateEngagementRequestFactory::new()->create();

    app(CreateEngagement::class)->execute(new EngagementCreationData(
        user: $data['user'],
        recipient: $data['recipient'],
        channel: $data['channel'],
        subject: $data['subject'],
        body: $data['body'],
        scheduledAt: $scheduledAt = now()->addMinute(),
    ));

    assertDatabaseCount(Engagement::class, 1);

    expect(Engagement::first())
        ->user->is($data['user'])->toBeTrue()
        ->recipient->is($data['recipient'])->toBeTrue()
        ->channel->toBe($data['channel'])
        ->subject->toBe($data['subject'])
        ->body->toMatchArray($data['body'])
        ->scheduled_at->startOfSecond()->eq($scheduledAt->startOfSecond())->toBeTrue()
        ->dispatched_at->toBeNull();

    Notification::assertNotSentTo(
        $data['recipient'],
        EngagementNotification::class
    );
});
