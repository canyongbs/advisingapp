<?php

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

use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\ProspectInteractionUsersTable;
use AdvisingApp\Team\Models\Team;
use App\Models\User;
use Carbon\Carbon;

use function Pest\Livewire\livewire;

it('can see prospect interaction users table', function () {
    $team = Team::factory()->create();

    $user1 = User::factory()->create();

    $user2 = User::factory()->for($team, 'team')->create();

    $prospect = Prospect::factory()->create();

    Interaction::factory()
        ->count(5)
        ->for($prospect, 'interactable')
        ->for($user1, 'user')
        ->create([
            'created_at' => now(),
        ]);

    Interaction::factory()
        ->count(10)
        ->for($prospect, 'interactable')
        ->for($user2, 'user')
        ->create([
            'created_at' => now(),
        ]);

    $firstUserInteractionAt = $user1->interactions->sortBy('created_at')->first();
    $firstUserInteractionAtDate = $firstUserInteractionAt ? $firstUserInteractionAt->created_at->format('M d, Y') : null;

    $secondUserInteractionAt = $user2->interactions->sortBy('created_at')->first();
    $secondUserInteractionAtDate = $secondUserInteractionAt ? $secondUserInteractionAt->created_at->format('M d, Y') : null;

    $firstUserMostRecentInteractionAt = $user1->interactions->sortByDesc('created_at')->first();
    $firstUserMostRecentInteractionAtDate = $firstUserMostRecentInteractionAt ? $firstUserMostRecentInteractionAt->created_at->format('M d, Y') : null;

    $secondUserMostRecentInteractionAt = $user2->interactions->sortByDesc('created_at')->first();
    $secondUserMostRecentInteractionAtDate = $secondUserMostRecentInteractionAt ? $secondUserMostRecentInteractionAt->created_at->format('M d, Y') : null;

    $totalInteractionUser1 = $user1->interactions->count();
    $totalInteractionUser2 = $user2->interactions->count();

    $totalInteractions = $totalInteractionUser1 + $totalInteractionUser2;

    $totalPerUser1 = $totalInteractions > 0 ? round(($user1->interactions->count() / $totalInteractions) * 100) : 0;
    $totalPerUser2 = $totalInteractions > 0 ? round(($user2->interactions->count() / $totalInteractions) * 100) : 0;

    $durationsUser1 = $user1->interactions->map(function ($interaction) {
        return Carbon::parse($interaction->end_datetime)
            ->diffInMinutes(Carbon::parse($interaction->start_datetime), true);
    })->filter();

    $avgUser1 = round($durationsUser1->avg());

    $durationsUser2 = $user2->interactions->map(function ($interaction) {
        return Carbon::parse($interaction->end_datetime)
            ->diffInMinutes(Carbon::parse($interaction->start_datetime), true);
    })->filter();

    $avgUser2 = round($durationsUser2->avg());

    $tableRecords = collect([$user1, $user2]);

    livewire(ProspectInteractionUsersTable::class, ['cacheTag' => 'report-prospect-interaction'])
        ->assertCanSeeTableRecords($tableRecords)
        ->assertTableColumnStateSet('name', $user1->name, $user1)
        ->assertTableColumnStateSet('name', $user2->name, $user2)
        ->assertTableColumnStateSet('first_interaction_at', $firstUserInteractionAtDate, $user1)
        ->assertTableColumnStateSet('first_interaction_at', $secondUserInteractionAtDate, $user2)
        ->assertTableColumnStateSet('most_recent_interaction_at', $firstUserMostRecentInteractionAtDate, $user1)
        ->assertTableColumnStateSet('most_recent_interaction_at', $secondUserMostRecentInteractionAtDate, $user2)
        ->assertTableColumnStateSet('total_interactions', $totalInteractionUser1, $user1)
        ->assertTableColumnStateSet('total_interactions', $totalInteractionUser2, $user2)
        ->assertTableColumnStateSet('total_interactions_percent', "{$totalPerUser1}%", $user1)
        ->assertTableColumnStateSet('total_interactions_percent', "{$totalPerUser2}%", $user2)
        ->assertTableColumnStateSet('avg_interaction_duration', "{$avgUser1} Min.", $user1)
        ->assertTableColumnStateSet('avg_interaction_duration', "{$avgUser2} Min.", $user2);
});

it('can filter users by name', function () {
    $team = Team::factory()->create([
        'name' => 'Test Team',
    ]);

    $user1 = User::factory()->create([
        'name' => 'Super Admin',
        'job_title' => 'Computer Operator',
    ]);

    $user2 = User::factory()->for($team, 'team')->create([
        'name' => 'Canyon GBS',
        'job_title' => 'CEO',
    ]);

    $prospect = Prospect::factory()->create();

    Interaction::factory()
        ->count(5)
        ->for($prospect, 'interactable')
        ->for($user1, 'user')
        ->create([
            'created_at' => now(),
        ]);

    Interaction::factory()
        ->count(10)
        ->for($prospect, 'interactable')
        ->for($user2, 'user')
        ->create([
            'created_at' => now(),
        ]);

    livewire(ProspectInteractionUsersTable::class, ['cacheTag' => 'report-prospect-interaction'])
        ->filterTable('name', [
            'name' => 'Super Admin',
        ])
        ->assertCanSeeTableRecords(collect([$user1]))
        ->assertCanNotSeeTableRecords(collect([$user2]));
});

it('can filter users by job title', function () {
    $team = Team::factory()->create([
        'name' => 'Test Team',
    ]);

    $user1 = User::factory()->create([
        'name' => 'Super Admin',
        'job_title' => 'Computer Operator',
    ]);

    $user2 = User::factory()->for($team, 'team')->create([
        'name' => 'Canyon GBS',
        'job_title' => 'CEO',
    ]);

    $prospect = Prospect::factory()->create();

    Interaction::factory()
        ->count(5)
        ->for($prospect, 'interactable')
        ->for($user1, 'user')
        ->create([
            'created_at' => now(),
        ]);

    Interaction::factory()
        ->count(10)
        ->for($prospect, 'interactable')
        ->for($user2, 'user')
        ->create([
            'created_at' => now(),
        ]);

    livewire(ProspectInteractionUsersTable::class, ['cacheTag' => 'report-prospect-interaction'])
        ->filterTable('job_title', [
            'job_title' => 'Computer Operator',
        ])
        ->assertCanSeeTableRecords(collect([$user1]))
        ->assertCanNotSeeTableRecords(collect([$user2]));
});

it('can filter users by team', function () {
    $team = Team::factory()->create([
        'name' => 'Interaction Team',
    ]);

    $user1 = User::factory()->create([
        'name' => 'Super Admin',
        'job_title' => 'Computer Operator',
    ]);

    $user2 = User::factory()->for($team, 'team')->create([
        'name' => 'Canyon GBS',
        'job_title' => 'CEO',
    ]);

    $prospect = Prospect::factory()->create();

    Interaction::factory()
        ->count(5)
        ->for($prospect, 'interactable')
        ->for($user1, 'user')
        ->create([
            'created_at' => now(),
        ]);

    Interaction::factory()
        ->count(10)
        ->for($prospect, 'interactable')
        ->for($user2, 'user')
        ->create([
            'created_at' => now(),
        ]);

    livewire(ProspectInteractionUsersTable::class, ['cacheTag' => 'report-prospect-interaction'])
        ->filterTable('team', [
            'team' => $team->getKey(),
        ])
        ->assertCanSeeTableRecords(collect([$user2]))
        ->assertCanNotSeeTableRecords(collect([$user1]));
});

it('displays only users with prospect interactions within the selected date range', function () {
    $interactionStartDate = now()->subDays(10);
    $interactionEndDate = now()->subDays(5);

    $team = Team::factory()->create();

    $userWithOldInteractions = User::factory()->create();

    $userWithRecentAndOtherInteractions = User::factory()->for($team, 'team')->create();

    $userWithoutInteractions = User::factory()->create();

    $prospect = Prospect::factory()->create();

    Interaction::factory()
        ->count(5)
        ->for($prospect, 'interactable')
        ->for($userWithOldInteractions, 'user')
        ->state([
            'created_at' => $interactionStartDate,
        ])
        ->create();

    Interaction::factory()
        ->count(10)
        ->for($prospect, 'interactable')
        ->for($userWithRecentAndOtherInteractions, 'user')
        ->state([
            'created_at' => $interactionEndDate,
        ])
        ->create();

    Interaction::factory()
        ->count(10)
        ->for($prospect, 'interactable')
        ->for($userWithRecentAndOtherInteractions, 'user')
        ->create();

    $filters = [
        'startDate' => $interactionStartDate->toDateString(),
        'endDate' => $interactionEndDate->toDateString(),
    ];

    livewire(ProspectInteractionUsersTable::class, [
        'cacheTag' => 'report-prospect-interaction',
        'pageFilters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $userWithOldInteractions,
            $userWithRecentAndOtherInteractions,
        ]))
        ->assertCanNotSeeTableRecords(collect([$userWithoutInteractions]));
});

it('displays only users with prospect interactions based on segment filter', function () {
    $segment = Group::factory()->create([
        'model' => GroupModel::Prospect,
        'filters' => [
            'queryBuilder' => [
                'rules' => [
                    'C0Cy' => [
                        'type' => 'last_name',
                        'data' => [
                            'operator' => 'contains',
                            'settings' => [
                                'text' => 'John',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    $team = Team::factory()->create();

    $userWithOldInteractions = User::factory()->create();

    $userWithRecentAndOtherInteractions = User::factory()->for($team, 'team')->create();

    $userWithoutInteractions = User::factory()->create();

    $prospectOne = Prospect::factory()->create([
        'last_name' => 'John',
    ]);

    $prospectTwo = Prospect::factory()->create([
        'last_name' => 'Doe',
    ]);

    Interaction::factory()
        ->count(5)
        ->for($prospectOne, 'interactable')
        ->for($userWithOldInteractions, 'user')
        ->create();

    Interaction::factory()
        ->count(10)
        ->for($prospectTwo, 'interactable')
        ->for($userWithRecentAndOtherInteractions, 'user')
        ->create();

    $filters = [
        'populationGroup' => $segment->getKey(),
    ];

    // with segment filter
    livewire(ProspectInteractionUsersTable::class, [
        'cacheTag' => 'report-prospect-interaction',
        'pageFilters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $userWithOldInteractions,
        ]))
        ->assertCanNotSeeTableRecords(collect([$userWithoutInteractions, $userWithRecentAndOtherInteractions]));

    // without filter
    livewire(ProspectInteractionUsersTable::class, [
        'cacheTag' => 'report-prospect-interaction',
        'filters' => [],
    ])
        ->assertCanSeeTableRecords(collect([
            $userWithOldInteractions, $userWithRecentAndOtherInteractions,
        ]))
        ->assertCanNotSeeTableRecords(collect([$userWithoutInteractions]));
});
