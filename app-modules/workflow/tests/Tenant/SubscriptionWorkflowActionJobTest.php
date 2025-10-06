<?php

namespace AdvisingApp\Workflow\Tests\Tenant;

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

use AdvisingApp\Notification\Actions\SubscriptionCreate;
use AdvisingApp\Notification\Models\Contracts\Subscribable;
use AdvisingApp\Notification\Models\Subscription;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Workflow\Jobs\SubscriptionWorkflowActionJob;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use AdvisingApp\Workflow\Models\WorkflowSubscriptionDetails;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

it('will execute appropriately on each educatable in the segment', function (array $priorSubscriptions, Educatable&Subscribable $educatable, bool $removePrior) {
    Bus::fake();
    $user = User::factory()->create();

    $workflowTrigger = WorkflowTrigger::factory()->create([
        'created_by_type' => User::class,
        'created_by_id' => $user->id,
    ]);

    $workflowRun = WorkflowRun::factory()->create([
        'workflow_trigger_id' => $workflowTrigger->id,
        'related_type' => $educatable->getMorphClass(),
        'related_id' => $educatable->getKey(),
    ]);

    collect($priorSubscriptions)
        ->each(
            fn ($userId) => resolve(SubscriptionCreate::class)
                ->handle(User::find($userId), $educatable)
        );
    $users = User::factory()->count(3)->create();

    $subscriptionDetails = WorkflowSubscriptionDetails::factory()->create([
        'user_ids' => $users->pluck('id')->toArray(),
        'remove_prior' => $removePrior,
    ]);

    $workflowRunStep = WorkflowRunStep::factory()->withDetails($subscriptionDetails)->create([
        'workflow_run_id' => $workflowRun->id,
        'execute_at' => now(),
    ]);

    expect($workflowRunStep->succeeded_at)->toBeNull()
        ->and($workflowRunStep->last_failed_at)->toBeNull();

    [$job] = (new SubscriptionWorkflowActionJob($workflowRunStep))->withFakeBatch();

    $job->handle();

    expect($educatable->subscriptions()->pluck('user_id')->toArray())
        ->toEqualCanonicalizing(
            $removePrior
                ? $users->pluck('id')->toArray()
                : [...$priorSubscriptions, ...$users->pluck('id')->toArray()]
        );

    expect($workflowRunStep->succeeded_at)->not()->toBeNull()
        ->and($workflowRunStep->last_failed_at)->toBeNull();

    $relatedModel = $workflowRunStep->workflowRun->related;
    assert($relatedModel instanceof Subscribable);
    expect($relatedModel->subscriptions()->get())->toHaveCount(
        $removePrior
            ? $users->count()
            : count($priorSubscriptions) + $users->count()
    );

    $expectedUserIds = $removePrior
        ? $users->pluck('id')->toArray()
        : [...$priorSubscriptions, ...$users->pluck('id')->toArray()];

    $relatedModel->subscriptions()
        ->each(function (Subscription $subscription) use ($expectedUserIds) {
            expect($subscription)->toBeInstanceOf(Subscription::class);

            /** @var Subscription $subscription */
            expect($subscription->user->getKey())->toBeIn($expectedUserIds)
                ->and($subscription->subscribable->is($subscription->subscribable))->toBeTrue();
        });
})->with(
    [
        'no prior subscriptions | prospect | remove prior false' => [
            [],
            fn () => Prospect::factory()->create(),
            false,
        ],
        'no prior subscriptions | prospect | remove prior true' => [
            [],
            fn () => Prospect::factory()->create(),
            true,
        ],
        'prior subscriptions | prospect | remove prior false' => [
            fn () => User::factory()->create()->pluck('id')->toArray(),
            fn () => Prospect::factory()->create(),
            false,
        ],
        'prior subscriptions | prospect | remove prior true' => [
            fn () => User::factory()->create()->pluck('id')->toArray(),
            fn () => Prospect::factory()->create(),
            true,
        ],
        'no prior subscriptions | student | remove prior false' => [
            [],
            fn () => Student::factory()->create(),
            false,
        ],
        'no prior subscriptions | student | remove prior true' => [
            [],
            fn () => Student::factory()->create(),
            true,
        ],
        'prior subscriptions | student | remove prior false' => [
            fn () => User::factory()->create()->pluck('id')->toArray(),
            fn () => Student::factory()->create(),
            false,
        ],
        'prior subscriptions | student | remove prior true' => [
            fn () => User::factory()->create()->pluck('id')->toArray(),
            fn () => Student::factory()->create(),
            true,
        ],
    ]
);
