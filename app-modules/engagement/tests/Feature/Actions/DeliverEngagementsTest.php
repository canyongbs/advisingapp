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

use Carbon\Carbon;
use Illuminate\Support\Facades\Queue;
use Assist\Engagement\Models\Engagement;
use Illuminate\Support\Facades\Notification;
use Assist\Engagement\Actions\DeliverEngagements;
use Assist\Engagement\Models\EngagementDeliverable;
use Assist\Engagement\Actions\EngagementSmsChannelDelivery;
use Assist\Engagement\Actions\EngagementEmailChannelDelivery;

it('will dispatch a job to send all engagements that should be delivered via email', function () {
    Queue::fake(EngagementEmailChannelDelivery::class);

    // Given that we have an engagement that should be delivered
    $engagement = Engagement::factory()
        ->deliverNow()
        ->has(EngagementDeliverable::factory()->email()->count(1))
        ->create();

    // And an engagement that shouldn't be sent until some point in the future
    $futureEngagement = Engagement::factory()
        ->deliverLater()
        ->has(EngagementDeliverable::factory()->email()->count(1))
        ->create();

    // When we dispatch our job to deliver engagements
    DeliverEngagements::dispatchSync();

    // A job to "send" the engagement should be dispatched for only the first engagement
    Queue::assertPushed(EngagementEmailChannelDelivery::class, function ($job) use ($engagement) {
        return $job->deliverable->is($engagement->deliverables->first());
    });

    Queue::assertNotPushed(EngagementEmailChannelDelivery::class, function ($job) use ($futureEngagement) {
        return $job->deliverable->is($futureEngagement->deliverables->first());
    });
});

it('will dispatch a job to send all engagements that should be delivered via sms', function () {
    Queue::fake(EngagementSmsChannelDelivery::class);

    // Given that we have an engagement that should be delivered
    $engagement = Engagement::factory()
        ->deliverNow()
        ->has(EngagementDeliverable::factory()->sms()->count(1))
        ->create();

    // And an engagement that shouldn't be sent until some point in the future
    $futureEngagement = Engagement::factory()
        ->deliverLater()
        ->has(EngagementDeliverable::factory()->sms()->count(1))
        ->create();

    // When we dispatch our job to deliver engagements
    DeliverEngagements::dispatchSync();

    // A job to "send" the engagement should be dispatched for only the first engagement
    Queue::assertPushed(EngagementSmsChannelDelivery::class, function ($job) use ($engagement) {
        return $job->deliverable->is($engagement->deliverables->first());
    });

    Queue::assertNotPushed(EngagementSmsChannelDelivery::class, function ($job) use ($futureEngagement) {
        return $job->deliverable->is($futureEngagement->deliverables->first());
    });
});

it('will not dispatch a job to send an engagement that has already been delivered', function () {
    Queue::fake(EngagementEmailChannelDelivery::class);
    Notification::fake();

    // Given that we have an engagement
    $engagement = Engagement::factory()
        ->deliverNow()
        ->has(EngagementDeliverable::factory()->email()->count(1))
        ->create();

    // And it has already been delivered
    DeliverEngagements::dispatchSync();
    $engagement->deliverables->first()->markDeliverySuccessful();

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
        ->has(EngagementDeliverable::factory()->email()->count(1))
        ->create();

    // When our job runs to pick up engagements
    DeliverEngagements::dispatchSync();

    // This engagement should not be picked up and delivered
    Queue::assertPushed(EngagementEmailChannelDelivery::class, 0);
});

it('will only dispatch a job to send an engagement that is scheduled', function () {
    Queue::fake(EngagementEmailChannelDelivery::class);
    Notification::fake();

    // Given that we have an engagement that is not scheduled but should otherwise be delivered
    Engagement::factory()
        ->onDemand()
        ->deliverNow()
        ->has(EngagementDeliverable::factory()->email()->count(1))
        ->create();

    // When our job runs to pick up engagements
    DeliverEngagements::dispatchSync();

    // This engagement should not be picked up and delivered
    Queue::assertPushed(EngagementEmailChannelDelivery::class, 0);
});
