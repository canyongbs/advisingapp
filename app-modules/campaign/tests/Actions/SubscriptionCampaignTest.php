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
use Assist\Campaign\Models\Campaign;
use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Assist\Campaign\Models\CampaignAction;
use Illuminate\Database\Eloquent\Collection;
use Assist\Campaign\Enums\CampaignActionType;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\CaseloadManagement\Enums\CaseloadType;
use Assist\Notifications\Actions\SubscriptionCreate;
use Assist\Notifications\Models\Contracts\Subscribable;

it('will create the subscription records for subscribables in the caseload', function (array $priorSubscriptions, Collection $subscribables, bool $removePrior) {
    $caseload = Caseload::factory()->create([
        'type' => CaseloadType::Static,
    ]);

    $subscribables->each(function (Subscribable $subscribable) use ($caseload, $priorSubscriptions) {
        $caseload->subjects()->create([
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

    $campaign = Campaign::factory()->create([
        'caseload_id' => $caseload->id,
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
