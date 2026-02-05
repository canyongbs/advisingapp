<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Task\Models\Task;
use AdvisingApp\Task\Notifications\TaskAssignedToUserNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
});

it('sends the proper notification to the assigned User', function () {
    $task = Task::factory()->assigned()->create(['is_confidential' => false]);

    Notification::assertSentTo($task->assignedTo, TaskAssignedToUserNotification::class);

    // Reset the fake notification store
    Notification::fake();

    $originalAssignedUser = $task->assignedTo;

    $newAssignedUser = User::factory()->create();

    $task->assignedTo()->associate($newAssignedUser)->save();

    Notification::assertSentTo($newAssignedUser, TaskAssignedToUserNotification::class);
    Notification::assertNotSentTo($originalAssignedUser, TaskAssignedToUserNotification::class);
});

it('it properly subscriptions the creator and assigned Users to the Subscribable', function () {
    $task = Task::factory()
        ->recycle(User::factory()->create())
        ->assigned()
        ->concerningStudent()
        ->create(['is_confidential' => false]);

    expect($task->createdBy->id)->toBe($task->assignedTo->id);

    $subscriptions = $task->createdBy->subscriptions;

    expect($subscriptions->count())->toBe(1)
        ->and($subscriptions->first()->subscribable->getKey())->toBe($task->concern->id);

    $task = Task::factory()
        ->assigned()
        ->concerningProspect()
        ->create(['is_confidential' => false]);

    expect($task->createdBy->id)->not->toBe($task->assignedTo->id);

    $creatorSubscriptions = $task->createdBy->subscriptions;

    expect($creatorSubscriptions->count())->toBe(1)
        ->and($creatorSubscriptions->first()->subscribable->getKey())->toBe($task->concern->id);

    $assignedToSubscriptions = $task->assignedTo->subscriptions;

    expect($assignedToSubscriptions->count())->toBe(1)
        ->and($assignedToSubscriptions->first()->subscribable->getKey())->toBe($task->concern->id);

    $newAssignedUser = User::factory()->create();

    $task->assignedTo()->associate($newAssignedUser)->save();

    $task->refresh();

    expect($task->createdBy->id)->not->toBe($task->assignedTo->id)
        ->and($task->assignedTo->id)->toBe($newAssignedUser->id);

    $creatorSubscriptions = $task->createdBy->subscriptions;

    expect($creatorSubscriptions->count())->toBe(1)
        ->and($creatorSubscriptions->first()->subscribable->getKey())->toBe($task->concern->id);

    $assignedToSubscriptions = $task->assignedTo->subscriptions;

    expect($assignedToSubscriptions->count())->toBe(1)
        ->and($assignedToSubscriptions->first()->subscribable->getKey())->toBe($task->concern->id);
});
