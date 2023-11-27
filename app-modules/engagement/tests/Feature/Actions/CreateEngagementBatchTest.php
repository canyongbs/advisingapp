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
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Assist\Engagement\Models\Engagement;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Support\Facades\Notification;
use Assist\Engagement\Models\EngagementBatch;
use Assist\Engagement\Models\EngagementDeliverable;
use Assist\Engagement\Actions\CreateEngagementBatch;
use Assist\Engagement\Enums\EngagementDeliveryMethod;
use Assist\Engagement\Actions\EngagementSmsChannelDelivery;
use Assist\Engagement\Actions\EngagementEmailChannelDelivery;
use Assist\Engagement\DataTransferObjects\EngagementBatchCreationData;
use Assist\Engagement\Notifications\EngagementBatchFinishedNotification;

it('will create a new engagement batch', function () {
    Queue::fake([EngagementEmailChannelDelivery::class, EngagementSmsChannelDelivery::class]);
    Notification::fake();

    CreateEngagementBatch::dispatch(EngagementBatchCreationData::from([
        'user' => User::factory()->create(),
        'records' => Student::factory()->count(1)->create(),
        'subject' => 'Test Subject',
        'body' => 'Test Body',
        'deliveryMethod' => EngagementDeliveryMethod::Email->value,
    ]));

    expect(EngagementBatch::count())->toBe(1);
});

it('will create an engagement for every record provided', function () {
    Queue::fake([EngagementEmailChannelDelivery::class, EngagementSmsChannelDelivery::class]);
    Notification::fake();

    CreateEngagementBatch::dispatch(EngagementBatchCreationData::from([
        'user' => User::factory()->create(),
        'records' => Student::factory()->count(3)->create(),
        'subject' => 'Test Subject',
        'body' => 'Test Body',
        'deliveryMethod' => EngagementDeliveryMethod::Email->value,
    ]));

    expect(Engagement::count())->toBe(3);
    expect(Student::first()->engagements()->count())->toBe(1);
});

it('will associate the engagement with the batch', function () {
    Queue::fake([EngagementEmailChannelDelivery::class, EngagementSmsChannelDelivery::class]);
    Notification::fake();

    CreateEngagementBatch::dispatch(EngagementBatchCreationData::from([
        'user' => User::factory()->create(),
        'records' => Student::factory()->count(4)->create(),
        'subject' => 'Test Subject',
        'body' => 'Test Body',
        'deliveryMethod' => EngagementDeliveryMethod::Email->value,
    ]));

    expect(EngagementBatch::first()->engagements()->count())->toBe(4);
});

it('will create deliverables for the created engagements', function () {
    Queue::fake([EngagementEmailChannelDelivery::class, EngagementSmsChannelDelivery::class]);
    Notification::fake();

    CreateEngagementBatch::dispatch(EngagementBatchCreationData::from([
        'user' => User::factory()->create(),
        'records' => Student::factory()->count(1)->create(),
        'subject' => 'Test Subject',
        'body' => 'Test Body',
        'deliveryMethod' => EngagementDeliveryMethod::Email->value,
    ]));

    expect(EngagementDeliverable::count())->toBe(1);
    expect(Engagement::first()->deliverable()->count())->toBe(1);
});

it('will dispatch a batch of jobs for each engagement that needs to be delivered', function () {
    Notification::fake();
    Bus::fake([EngagementEmailChannelDelivery::class, EngagementSmsChannelDelivery::class]);

    CreateEngagementBatch::dispatch(EngagementBatchCreationData::from([
        'user' => User::factory()->create(),
        'records' => Student::factory()->count(5)->create(),
        'subject' => 'Test Subject',
        'body' => 'Test Body',
        'deliveryMethod' => EngagementDeliveryMethod::Email->value,
    ]));

    Bus::assertBatched(function (PendingBatch $batch) {
        if ($batch->jobs->count() !== 5) {
            return false;
        }

        return $batch->jobs->every(function ($job) {
            return $job instanceof EngagementEmailChannelDelivery;
        });

        return true;
    });
});

it('will dispatch a notification to the user who initiated the batch engagement when the queue batch has finished processing', function () {
    Notification::fake();

    $user = User::factory()->create();

    CreateEngagementBatch::dispatch(EngagementBatchCreationData::from([
        'user' => $user,
        'records' => Student::factory()->count(1)->create(),
        'subject' => 'Test Subject',
        'body' => 'Test Body',
        'deliveryMethod' => EngagementDeliveryMethod::Email->value,
    ]));

    Notification::assertSentTo($user, EngagementBatchFinishedNotification::class);
});
