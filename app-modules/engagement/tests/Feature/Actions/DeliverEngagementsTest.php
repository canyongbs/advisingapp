<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Engagement\Actions\DeliverEngagements;
use AdvisingApp\Engagement\Actions\EngagementEmailChannelDelivery;
use AdvisingApp\Engagement\Actions\EngagementSmsChannelDelivery;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementDeliverable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

it('will dispatch a job to send all engagements that should be delivered via email', function () {
    Queue::fake(EngagementEmailChannelDelivery::class);

    // Given that we have an engagement that should be delivered
    $engagement = Engagement::factory()
        ->deliverNow()
        ->has(EngagementDeliverable::factory()->email()->count(1), 'deliverable')
        ->create();

    // And an engagement that shouldn't be sent until some point in the future
    $futureEngagement = Engagement::factory()
        ->deliverLater()
        ->has(EngagementDeliverable::factory()->email()->count(1), 'deliverable')
        ->create();

    // When we dispatch our job to deliver engagements
    DeliverEngagements::dispatchSync();

    // A job to "send" the engagement should be dispatched for only the first engagement
    Queue::assertPushed(EngagementEmailChannelDelivery::class, function ($job) use ($engagement) {
        return $job->deliverable->is($engagement->deliverable);
    });

    Queue::assertNotPushed(EngagementEmailChannelDelivery::class, function ($job) use ($futureEngagement) {
        return $job->deliverable->is($futureEngagement->deliverable);
    });
});

it('will dispatch a job to send all engagements that should be delivered via sms', function () {
    Queue::fake(EngagementSmsChannelDelivery::class);

    // Given that we have an engagement that should be delivered
    $engagement = Engagement::factory()
        ->deliverNow()
        ->has(EngagementDeliverable::factory()->sms()->count(1), 'deliverable')
        ->create();

    // And an engagement that shouldn't be sent until some point in the future
    $futureEngagement = Engagement::factory()
        ->deliverLater()
        ->has(EngagementDeliverable::factory()->sms()->count(1), 'deliverable')
        ->create();

    // When we dispatch our job to deliver engagements
    DeliverEngagements::dispatchSync();

    // A job to "send" the engagement should be dispatched for only the first engagement
    Queue::assertPushed(EngagementSmsChannelDelivery::class, function ($job) use ($engagement) {
        return $job->deliverable->is($engagement->deliverable);
    });

    Queue::assertNotPushed(EngagementSmsChannelDelivery::class, function ($job) use ($futureEngagement) {
        return $job->deliverable->is($futureEngagement->deliverable);
    });
});

it('will not dispatch a job to send an engagement that has already been delivered', function () {
    Queue::fake(EngagementEmailChannelDelivery::class);
    Notification::fake();

    // Given that we have an engagement
    $engagement = Engagement::factory()
        ->deliverNow()
        ->has(EngagementDeliverable::factory()->email()->count(1), 'deliverable')
        ->create();

    // And it has already been delivered
    DeliverEngagements::dispatchSync();
    $engagement->deliverable->markDeliverySuccessful();

    Queue::assertPushed(EngagementEmailChannelDelivery::class, 1);

    Carbon::setTestNow(now()->addMinute());

    // When our job runs again to pick up more engagements
    DeliverEngagements::dispatchSync();

    // A job to "send" the engagement should not be dispatched again
    Queue::assertPushed(EngagementEmailChannelDelivery::class, 1);
});

it('will not dispatch a job to send an engagement that is part of a batch', function () {
    Queue::fake(EngagementEmailChannelDelivery::class);
    Notification::fake();

    // Given that we have an engagement
    Engagement::factory()
        ->ofBatch()
        ->deliverNow()
        ->has(EngagementDeliverable::factory()->email()->count(1), 'deliverable')
        ->create();

    // When our job runs to pick up engagements
    DeliverEngagements::dispatchSync();

    // This engagement should not be picked up and delivered
    Queue::assertPushed(EngagementEmailChannelDelivery::class, 0);
});
