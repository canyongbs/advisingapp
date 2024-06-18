<?php

use Illuminate\Support\Facades\Queue;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\IntegrationTwilio\Jobs\CheckSmsOutboundDeliverableStatus;
use AdvisingApp\IntegrationTwilio\Jobs\CheckStatusOfOutboundDeliverablesWithoutATerminalStatus;

it('will only check the status of outbound deliverables with non terminal external statuses', function () {
    Queue::fake([CheckSmsOutboundDeliverableStatus::class]);

    // Given that we have a deliverable with a non-terminal status that should be processed
    $deliverableThatShouldBeProcessed = OutboundDeliverable::factory()->create([
        'channel' => 'sms',
        'external_status' => 'queued',
        'last_delivery_attempt' => now()->subHours(25),
    ]);

    // And another deliverable with a terminal status that should not be processed
    OutboundDeliverable::factory()->create([
        'channel' => 'sms',
        'external_status' => 'delivered',
        'last_delivery_attempt' => now()->subHours(25),
    ]);

    // When we check for deliverables to check the status of
    CheckStatusOfOutboundDeliverablesWithoutATerminalStatus::dispatchSync();

    // We should only have 1 job dispatched
    Queue::assertPushed(CheckSmsOutboundDeliverableStatus::class, 1);

    // And the job should be for the deliverable that should be processed
    Queue::assertPushed(function (CheckSmsOutboundDeliverableStatus $job) use ($deliverableThatShouldBeProcessed) {
        return $job->deliverable->id === $deliverableThatShouldBeProcessed->id;
    });
});

it('will only check the status of outbound deliverables with non terminal statuses in which the last delivery attempt was over 24 hours ago', function () {
    Queue::fake([CheckSmsOutboundDeliverableStatus::class]);

    // Given that we have a deliverable with a non-terminal status that should be processed
    $deliverableThatShouldBeProcessed = OutboundDeliverable::factory()->create([
        'channel' => 'sms',
        'external_status' => 'queued',
        'last_delivery_attempt' => now()->subHours(25),
    ]);

    // And another deliverable with a non-terminal status that should not be processed
    OutboundDeliverable::factory()->create([
        'channel' => 'sms',
        'external_status' => 'queued',
        'last_delivery_attempt' => now()->subHours(23),
    ]);

    // When we check for deliverables to check the status of
    CheckStatusOfOutboundDeliverablesWithoutATerminalStatus::dispatchSync();

    // We should only have 1 job dispatched
    Queue::assertPushed(CheckSmsOutboundDeliverableStatus::class, 1);

    // And the job should be for the deliverable that should be processed
    Queue::assertPushed(function (CheckSmsOutboundDeliverableStatus $job) use ($deliverableThatShouldBeProcessed) {
        return $job->deliverable->id === $deliverableThatShouldBeProcessed->id;
    });
});

it('will not check the status of an outbound deliverable whose last delivery attempt was more than 7 days ago', function () {
    Queue::fake([CheckSmsOutboundDeliverableStatus::class]);

    // Given that we have a deliverable with a non-terminal status that should not be processed
    OutboundDeliverable::factory()->create([
        'channel' => 'sms',
        'external_status' => 'queued',
        'last_delivery_attempt' => now()->subHours(169),
    ]);

    // When we check for deliverables to check the status of
    CheckStatusOfOutboundDeliverablesWithoutATerminalStatus::dispatchSync();

    // We should not have any jobs dispatched
    Queue::assertNotPushed(CheckSmsOutboundDeliverableStatus::class);
});
