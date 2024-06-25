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
use Laravel\Pennant\Feature;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Segment\Enums\SegmentType;
use Illuminate\Database\Eloquent\Collection;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\CaseloadManagement\Models\Caseload;
use AdvisingApp\CaseloadManagement\Enums\CaseloadType;
use AdvisingApp\Notification\Actions\SubscriptionCreate;
use AdvisingApp\Notification\Models\Contracts\Subscribable;

it('will create the subscription records for subscribables in the caseload', function (array $priorSubscriptions, Collection $subscribables, bool $removePrior) {
    Feature::active('enable-segments')
        ? $segmentOrCaseload = Segment::factory()->create([
            'type' => SegmentType::Static,
        ])
        : $segmentOrCaseload = Caseload::factory()->create([
            'type' => CaseloadType::Static,
        ]);

    $subscribables->each(function (Subscribable $subscribable) use ($segmentOrCaseload, $priorSubscriptions) {
        $segmentOrCaseload->subjects()->create([
            'subject_id' => $subscribable->getKey(),
            'subject_type' => $subscribable->getMorphClass(),
        ]);

        $subscribable->subscriptions()->delete();

        collect($priorSubscriptions)
            ->each(
                fn ($userId) => resolve(SubscriptionCreate::class)
                    ->handle(User::find($userId), $subscribable, false)
            );
    });

    Feature::active('enable-segments')
        ? $foreignKey = 'segment_id'
        : $foreignKey = 'caseload_id';

    $campaign = Campaign::factory()->create([
        $foreignKey => $segmentOrCaseload->id,
    ]);

    $users = User::factory()->count(3)->create();

    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::Subscription,
            'data' => [
                'user_ids' => $users->pluck('id')->toArray(),
                'remove_prior' => $removePrior,
            ],
        ]);

    // When that action runs
    $action->execute();

    $subscribables->each(
        function (Subscribable $subscribable) use ($removePrior, $users, $priorSubscriptions) {
            return expect(
                $subscribable->subscriptions()->pluck('user_id')->toArray()
            )
                ->toBe(
                    $removePrior
                        ? $users->pluck('id')->toArray()
                        : [...$priorSubscriptions, ...$users->pluck('id')->toArray()]
                );
        }
    );
})->with(
    [
        'no prior subscriptions | prospects | remove prior false' => [
            'priorSubscriptions' => [],
            'subscribables' => fn () => Prospect::factory()->count(3)->create(),
            'removePrior' => false,
        ],
        'no prior subscriptions | prospects | remove prior true' => [
            'priorSubscriptions' => [],
            'subscribables' => fn () => Prospect::factory()->count(3)->create(),
            'removePrior' => true,
        ],
        'prior subscriptions | prospects | remove prior false' => [
            'priorSubscriptions' => fn () => User::factory()->count(3)->create()->pluck('id')->toArray(),
            'subscribables' => fn () => Prospect::factory()->count(3)->create(),
            'removePrior' => false,
        ],
        'prior subscriptions | prospects | remove prior true' => [
            'priorSubscriptions' => fn () => User::factory()->count(3)->create()->pluck('id')->toArray(),
            'subscribables' => fn () => Prospect::factory()->count(3)->create(),
            'removePrior' => true,
        ],
        'no prior subscriptions | students | remove prior false' => [
            'priorSubscriptions' => [],
            'subscribables' => fn () => Student::factory()->count(3)->create(),
            'removePrior' => false,
        ],
        'no prior subscriptions | students | remove prior true' => [
            'priorSubscriptions' => [],
            'subscribables' => fn () => Student::factory()->count(3)->create(),
            'removePrior' => true,
        ],
        'prior subscriptions | students | remove prior false' => [
            'priorSubscriptions' => fn () => User::factory()->count(3)->create()->pluck('id')->toArray(),
            'subscribables' => fn () => Student::factory()->count(3)->create(),
            'removePrior' => false,
        ],
        'prior subscriptions | students | remove prior true' => [
            'priorSubscriptions' => fn () => User::factory()->count(3)->create()->pluck('id')->toArray(),
            'subscribables' => fn () => Student::factory()->count(3)->create(),
            'removePrior' => true,
        ],
    ]
);
