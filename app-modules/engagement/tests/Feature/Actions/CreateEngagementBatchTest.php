<?php

use App\Models\User;
use Mockery\MockInterface;
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
use Assist\Engagement\Notifications\EngagementBatchFinishedNotification;

it('will create a new engagement batch', function () {
    Queue::fake([EngagementEmailChannelDelivery::class, EngagementSmsChannelDelivery::class]);
    Notification::fake();

    $user = User::factory()->create();
    $records = Student::factory()->count(3)->create();

    $data = [
        'subject' => 'Test Subject',
        'body' => 'Test Body',
        'delivery_methods' => [
            EngagementDeliveryMethod::EMAIL->value,
            EngagementDeliveryMethod::SMS->value,
        ],
    ];

    CreateEngagementBatch::dispatchSync($user, $records, $data);

    expect(EngagementBatch::count())->toBe(1);
});

it('will create an engagement for every record provided', function () {
    Queue::fake([EngagementEmailChannelDelivery::class, EngagementSmsChannelDelivery::class]);
    Notification::fake();

    $user = User::factory()->create();
    $records = Student::factory()->count(3)->create();

    $data = [
        'subject' => 'Test Subject',
        'body' => 'Test Body',
        'delivery_methods' => [
            EngagementDeliveryMethod::EMAIL->value,
            EngagementDeliveryMethod::SMS->value,
        ],
    ];

    CreateEngagementBatch::dispatchSync($user, $records, $data);

    expect(Engagement::count())->toBe(3);
    expect(Student::first()->engagements()->count())->toBe(1);
});

it('will associate the engagement with the batch', function () {
    Queue::fake([EngagementEmailChannelDelivery::class, EngagementSmsChannelDelivery::class]);
    Notification::fake();

    $user = User::factory()->create();
    $records = Student::factory()->count(3)->create();

    $data = [
        'subject' => 'Test Subject',
        'body' => 'Test Body',
        'delivery_methods' => [
            EngagementDeliveryMethod::EMAIL->value,
            EngagementDeliveryMethod::SMS->value,
        ],
    ];

    CreateEngagementBatch::dispatchSync($user, $records, $data);

    expect(EngagementBatch::first()->engagements()->count())->toBe(3);
});

it('will create deliverables for the created engagements', function () {
    Queue::fake([EngagementEmailChannelDelivery::class, EngagementSmsChannelDelivery::class]);
    Notification::fake();

    $user = User::factory()->create();
    $records = Student::factory()->count(1)->create();

    $data = [
        'subject' => 'Test Subject',
        'body' => 'Test Body',
        'delivery_methods' => [
            EngagementDeliveryMethod::EMAIL->value,
        ],
    ];

    CreateEngagementBatch::dispatchSync($user, $records, $data);

    expect(EngagementDeliverable::count())->toBe(1);
    expect(Engagement::first()->deliverables()->count())->toBe(1);
});

it('will dispatch a batch of jobs for each engagement that needs to be delivered', function () {
    Notification::fake();
    Bus::fake([EngagementEmailChannelDelivery::class, EngagementSmsChannelDelivery::class]);

    $user = User::factory()->create();
    $records = Student::factory()->count(3)->create();

    $data = [
        'subject' => 'Test Subject',
        'body' => 'Test Body',
        'delivery_methods' => [
            EngagementDeliveryMethod::EMAIL->value,
        ],
    ];

    CreateEngagementBatch::dispatch($user, $records, $data);

    Bus::assertBatched(function (PendingBatch $batch) {
        if ($batch->jobs->count() !== 3) {
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

    // The issue with this is that we're not resolving anything out of the container
    // So we cannot mock it... We'll have to find something in the path that we can resolve
    // $queuedDelivery = $this->partialMock(EngagementDeliverable::class, function (MockInterface $mock) {
    //     $mock->shouldReceive('deliver')
    //         ->once()
    //         ->andReturn();
    // });

    // ray('queuedDelivery', $queuedDelivery);

    $user = User::factory()->create();
    $records = Student::factory()->count(3)->create();

    $data = [
        'subject' => 'Test Subject',
        'body' => 'Test Body',
        'delivery_methods' => [
            EngagementDeliveryMethod::EMAIL->value,
        ],
    ];

    CreateEngagementBatch::dispatch($user, $records, $data);

    Notification::assertSentTo($user, EngagementBatchFinishedNotification::class);
});
