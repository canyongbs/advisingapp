<?php

use Illuminate\Support\Facades\Queue;
use Assist\Engagement\Models\Engagement;
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
