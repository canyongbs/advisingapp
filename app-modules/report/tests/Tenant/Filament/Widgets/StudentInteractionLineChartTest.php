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

use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Report\Filament\Widgets\StudentInteractionLineChart;
use AdvisingApp\StudentDataModel\Models\Student;
use Carbon\Carbon;

use function Pest\Laravel\travelTo;

it('returns correct data on a standard day', function () {
    travelTo(Carbon::parse('2024-12-15'));

    Student::factory()
        ->has(
            Interaction::factory()->count(13)->sequence(
                // Add interaction on Dec 31st of previous year to test that it should not be counted
                ['created_at' => Carbon::parse('2023-12-31')],
                ['created_at' => Carbon::parse('2024-01-15')],
                ['created_at' => Carbon::parse('2024-02-15')],
                // Add interaction on Feb 29th to test leap year handling
                ['created_at' => Carbon::parse('2024-02-29')],
                ['created_at' => Carbon::parse('2024-03-15')],
                // Add multiple interactions in March to test aggregation and throw in some days not all months have
                ['created_at' => Carbon::parse('2024-03-29')],
                ['created_at' => Carbon::parse('2024-03-30')],
                ['created_at' => Carbon::parse('2024-03-31')],
                ['created_at' => Carbon::parse('2024-04-15')],
                ['created_at' => Carbon::parse('2024-05-15')],
                // Add a gap in between in which no interactions occur
                ['created_at' => Carbon::parse('2024-10-15')],
                ['created_at' => Carbon::parse('2024-11-15')],
                ['created_at' => Carbon::parse('2024-12-01')],
            ),
            'interactions'
        )
        ->create();

    $widgetInstance = new StudentInteractionLineChart();
    $widgetInstance->cacheTag = 'report-student-interaction';

    expect($widgetInstance->getData())->toMatchSnapshot();
});

it('returns correct data on a non-standard day', function (Carbon $testDate) {
    travelTo($testDate);

    Student::factory()
        ->has(
            Interaction::factory()->count(13)->sequence(
                // Add interaction on in a month prior to test that it should not be counted
                ['created_at' => Carbon::parse('2023-08-31')],
                ['created_at' => Carbon::parse('2023-09-15')],
                ['created_at' => Carbon::parse('2023-10-15')],
                // Add a gap in between in which no interactions occur
                // Add interaction on Feb 29th to test leap year handling
                ['created_at' => Carbon::parse('2024-02-29')],
                ['created_at' => Carbon::parse('2024-03-15')],
                // Add multiple interactions in March to test aggregation and throw in some days not all months have
                ['created_at' => Carbon::parse('2024-03-29')],
                ['created_at' => Carbon::parse('2024-03-30')],
                ['created_at' => Carbon::parse('2024-03-31')],
                ['created_at' => Carbon::parse('2024-04-15')],
                ['created_at' => Carbon::parse('2024-05-15')],
                ['created_at' => Carbon::parse('2024-06-15')],
                ['created_at' => Carbon::parse('2024-07-15')],
                ['created_at' => Carbon::parse('2024-08-01')],
            ),
            'interactions'
        )
        ->create();

    $widgetInstance = new StudentInteractionLineChart();
    $widgetInstance->cacheTag = 'report-student-interaction';

    expect($widgetInstance->getData())->toMatchSnapshot();
})
    ->with([
        '31st of month' => [Carbon::parse('2024-08-31')],
        '30th of month' => [Carbon::parse('2024-08-30')],
        '29th of month' => [Carbon::parse('2024-08-29')],
    ]);

it('checks student interactions monthly line chart', function () {
    $studentCount = 5;

    Student::factory()->count($studentCount)->has(Interaction::factory()->count(5)->state([
        'created_at' => now()->subMonths(1),
    ]), 'interactions')->create();
    Student::factory()->count($studentCount)->has(Interaction::factory()->count(5)->state([
        'created_at' => now()->subMonths(6),
    ]), 'interactions')->create();

    $widgetInstance = new StudentInteractionLineChart();
    $widgetInstance->cacheTag = 'report-student-interaction';

    expect($widgetInstance->getData()['datasets'][0]['data'])->toMatchSnapshot();
})->skip();

it('returns correct data for student interactions within the given date range', function () {
    $interactionStartDate = now()->subMonths(3);
    $interactionEndDate = now()->subDays(5);

    Student::factory()->count(5)->has(
        Interaction::factory()
            ->count(5)
            ->state([
                'created_at' => $interactionStartDate,
            ]),
        'interactions'
    )->create();

    Student::factory()->count(5)->has(
        Interaction::factory()
            ->count(5)
            ->state([
                'created_at' => $interactionEndDate,
            ]),
        'interactions'
    )->create();

    $widgetInstance = new StudentInteractionLineChart();
    $widgetInstance->cacheTag = 'report-student-interaction';
    $widgetInstance->pageFilters = [
        'startDate' => $interactionStartDate->toDateString(),
        'endDate' => $interactionEndDate->toDateString(),
    ];

    expect($widgetInstance->getData()['datasets'][0]['data'])->toMatchSnapshot();
})->skip();

it('returns correct data for student interactions based on group filter', function () {
    $interactionStartDate = now()->subMonths(3);
    $interactionEndDate = now()->subDays(5);

    $group = Group::factory()->create([
        'model' => GroupModel::Student,
        'filters' => [
            'queryBuilder' => [
                'rules' => [
                    'C0Cy' => [
                        'type' => 'last',
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

    Student::factory()->count(5)->has(
        Interaction::factory()
            ->count(5)
            ->state([
                'created_at' => $interactionStartDate,
            ]),
        'interactions'
    )->create([
        'last' => 'John',
    ]);

    Student::factory()->count(5)->has(
        Interaction::factory()
            ->count(5)
            ->state([
                'created_at' => $interactionEndDate,
            ]),
        'interactions'
    )->create([
        'last' => 'Doe',
    ]);

    $widgetInstance = new StudentInteractionLineChart();
    $widgetInstance->cacheTag = 'report-student-interaction';
    $widgetInstance->pageFilters = [
        'populationGroup' => $group->getKey(),
    ];

    expect($widgetInstance->getData()['datasets'][0]['data'])->toMatchSnapshot();
})->skip();
