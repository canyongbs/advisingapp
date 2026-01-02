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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Pages\ProspectInteractionReport;
use AdvisingApp\Report\Filament\Widgets\ProspectInteractionStats;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

/** @var array<LicenseType> $licenses */
$licenses = [
    LicenseType::RecruitmentCrm,
];
$permission = [
    'report-library.view-any',
];

it('cannot render without a license', function () use ($permission) {
    actingAs(user(
        permissions: $permission
    ));

    get(ProspectInteractionReport::getUrl())
        ->assertForbidden();
});

it('cannot render without permissions', function () use ($licenses) {
    actingAs(user(
        licenses: $licenses
    ));

    get(ProspectInteractionReport::getUrl())
        ->assertForbidden();
});

it('can render', function () use ($licenses, $permission) {
    actingAs(user(
        licenses: $licenses,
        permissions: $permission
    ));

    get(ProspectInteractionReport::getUrl())
        ->assertSuccessful();
});

it('Check total interactions', function () {
    $interactionCount = rand(1, 10);
    $prospectInteractionStats = new ProspectInteractionStats();
    $prospectInteractionStats->cacheTag = 'report-prospect-interaction';

    Prospect::factory()
        ->has(Interaction::factory()->count($interactionCount), 'interactions')
        ->create();

    $stats = $prospectInteractionStats->getStats();
    $totalProspectInteractionsStat = $stats[0];
    expect($totalProspectInteractionsStat->getValue())->toEqual($interactionCount);
});

it('Check unique prospects with interactions', function () {
    $interactionCount = rand(1, 10);
    $prospectInteractionStats = new ProspectInteractionStats();
    $prospectInteractionStats->cacheTag = 'report-prospect-interaction';

    Prospect::factory()
        ->count($interactionCount)
        ->has(Interaction::factory()->count(1), 'interactions')
        ->create();

    $stats = $prospectInteractionStats->getStats();
    $totaluniqueProspectInteractionsStat = $stats[1];
    expect($totaluniqueProspectInteractionsStat->getValue())->toEqual($interactionCount);
});

it('returns correct total and unique prospect interaction counts within the given date range', function () {
    $prospectsWithStartDateInteractions = random_int(1, 10);
    $prospectsWithEndDateInteractions = random_int(1, 10);
    $interactionStartDate = now()->subDays(10);
    $interactionEndDate = now()->subDays(5);

    Prospect::factory()->count($prospectsWithStartDateInteractions)
        ->has(
            Interaction::factory()->state([
                'created_at' => $interactionStartDate,
            ]),
            'interactions'
        )->create();

    Prospect::factory()->count($prospectsWithEndDateInteractions)
        ->has(
            Interaction::factory()->state([
                'created_at' => $interactionEndDate,
            ]),
            'interactions'
        )->create();

    Prospect::factory()->count($prospectsWithEndDateInteractions)
        ->has(
            Interaction::factory()->count(2)->state([
                'created_at' => $interactionStartDate,
            ]),
            'interactions'
        )->create();

    $widget = new ProspectInteractionStats();
    $widget->cacheTag = 'report-prospect-interaction';
    $widget->pageFilters = [
        'startDate' => $interactionStartDate->toDateString(),
        'endDate' => $interactionEndDate->toDateString(),
    ];

    $stats = $widget->getStats();

    $prospectsTotalInteractionsStat = $stats[0];

    expect($prospectsTotalInteractionsStat->getValue())
        ->toEqual($prospectsWithStartDateInteractions + $prospectsWithEndDateInteractions + ($prospectsWithEndDateInteractions * 2));

    $prospectsWithInteractionsStat = $stats[1];

    expect($prospectsWithInteractionsStat->getValue())
        ->toEqual($prospectsWithStartDateInteractions + $prospectsWithEndDateInteractions + $prospectsWithEndDateInteractions);
});
it('returns correct total and unique prospect interaction counts based on group filter', function () {
    $prospectsWithJohnNameInteractions = random_int(1, 10);
    $prospectsWithDoeNameInteractions = random_int(1, 10);

    $group = Group::factory()->create([
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

    Prospect::factory()->count($prospectsWithJohnNameInteractions)
        ->has(
            Interaction::factory(),
            'interactions'
        )->create([
            'last_name' => 'John',
        ]);

    Prospect::factory()->count($prospectsWithDoeNameInteractions)
        ->has(
            Interaction::factory(),
            'interactions'
        )->create([
            'last_name' => 'Doe',
        ]);

    $widget = new ProspectInteractionStats();
    $widget->cacheTag = 'report-prospect-interaction';
    $widget->pageFilters = [
        'populationGroup' => $group->getKey(),
    ];

    $stats = $widget->getStats();

    $prospectsTotalInteractionsStat = $stats[0];

    expect($prospectsTotalInteractionsStat->getValue())
        ->toEqual($prospectsWithJohnNameInteractions);

    $prospectsWithInteractionsStat = $stats[1];

    expect($prospectsWithInteractionsStat->getValue())
        ->toEqual($prospectsWithJohnNameInteractions);

    // without group filter
    $widget = new ProspectInteractionStats();
    $widget->cacheTag = 'report-prospect-interaction';
    $widget->pageFilters = [];

    $stats = $widget->getStats();

    $prospectsTotalInteractionsStat = $stats[0];

    expect($prospectsTotalInteractionsStat->getValue())
        ->toEqual($prospectsWithJohnNameInteractions + $prospectsWithDoeNameInteractions);

    $prospectsWithInteractionsStat = $stats[1];

    expect($prospectsWithInteractionsStat->getValue())
        ->toEqual($prospectsWithJohnNameInteractions + $prospectsWithDoeNameInteractions);
});
