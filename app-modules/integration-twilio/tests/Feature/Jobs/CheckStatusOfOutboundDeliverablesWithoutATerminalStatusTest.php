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
