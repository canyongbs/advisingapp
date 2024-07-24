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

use App\Models\User;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Notification;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Engagement\Models\EngagementBatch;
use AdvisingApp\Engagement\Models\EngagementDeliverable;
use AdvisingApp\Engagement\Actions\CreateEngagementBatch;
use AdvisingApp\Engagement\Enums\EngagementDeliveryMethod;
use AdvisingApp\Engagement\Actions\EngagementSmsChannelDelivery;
use AdvisingApp\Engagement\Actions\EngagementEmailChannelDelivery;
use AdvisingApp\Engagement\DataTransferObjects\EngagementBatchCreationData;
use AdvisingApp\Engagement\Notifications\EngagementBatchFinishedNotification;

it('will create a new engagement batch', function () {
    Queue::fake([EngagementEmailChannelDelivery::class, EngagementSmsChannelDelivery::class]);
    Notification::fake();

    CreateEngagementBatch::dispatch(EngagementBatchCreationData::from([
        'user' => User::factory()->create(),
        'records' => Student::factory()->count(1)->create(),
        'subject' => 'Test Subject',
        'body' => ['content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Test Body']]]]],
        'deliveryMethod' => EngagementDeliveryMethod::Email->value,
    ]));

    expect(EngagementBatch::count())->toBe(1);
});

it('will create an engagement for every record provided', function () {
    Queue::fake([EngagementEmailChannelDelivery::class, EngagementSmsChannelDelivery::class]);
    Notification::fake();

    $students = Student::factory()->count(3)->create();

    CreateEngagementBatch::dispatch(EngagementBatchCreationData::from([
        'user' => User::factory()->create(),
        'records' => $students,
        'subject' => 'Test Subject',
        'body' => ['content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Test Body']]]]],
        'deliveryMethod' => EngagementDeliveryMethod::Email->value,
    ]));

    expect(Engagement::count())->toBe(3);

    $students->each(fn (Student $student) => expect($student->engagements()->count())->toBe(1));
});

it('will associate the engagement with the batch', function () {
    Queue::fake([EngagementEmailChannelDelivery::class, EngagementSmsChannelDelivery::class]);
    Notification::fake();

    CreateEngagementBatch::dispatch(EngagementBatchCreationData::from([
        'user' => User::factory()->create(),
        'records' => Student::factory()->count(4)->create(),
        'subject' => 'Test Subject',
        'body' => ['content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Test Body']]]]],
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
        'body' => ['content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Test Body']]]]],
        'deliveryMethod' => EngagementDeliveryMethod::Email->value,
    ]));

    expect(EngagementDeliverable::count())->toBe(1)
        ->and(Engagement::first()->deliverable()->count())->toBe(1);
});

it('will dispatch a batch of jobs for each engagement that needs to be delivered', function () {
    Notification::fake();
    Bus::fake([EngagementEmailChannelDelivery::class, EngagementSmsChannelDelivery::class]);

    CreateEngagementBatch::dispatch(EngagementBatchCreationData::from([
        'user' => User::factory()->create(),
        'records' => Student::factory()->count(5)->create(),
        'subject' => 'Test Subject',
        'body' => ['content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Test Body']]]]],
        'deliveryMethod' => EngagementDeliveryMethod::Email->value,
    ]));

    Bus::assertBatched(function (PendingBatch $batch) {
        if ($batch->jobs->count() !== 5) {
            return false;
        }

        return $batch->jobs->every(function ($job) {
            return $job instanceof EngagementEmailChannelDelivery;
        });
    });
});

it('will dispatch a notification to the user who initiated the batch engagement when the queue batch has finished processing', function () {
    Notification::fake();

    $user = User::factory()->create();

    CreateEngagementBatch::dispatch(EngagementBatchCreationData::from([
        'user' => $user,
        'records' => Student::factory()->count(1)->create(),
        'subject' => 'Test Subject',
        'body' => ['content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Test Body']]]]],
        'deliveryMethod' => EngagementDeliveryMethod::Email->value,
    ]));

    Notification::assertSentTo($user, EngagementBatchFinishedNotification::class);
});
