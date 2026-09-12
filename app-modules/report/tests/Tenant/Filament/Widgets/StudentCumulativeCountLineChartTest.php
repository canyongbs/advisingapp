<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Report\Filament\Widgets\StudentCumulativeCountLineChart;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Features\StudentArchivingFeature;
use Carbon\Carbon;

/**
 * Runs the chart with the given page filters and returns its series keyed by month label.
 * Dates are fixed rather than relative to `now()`, so the number of month buckets — and
 * therefore the expected series — never shifts with the calendar.
 *
 * @param array<string, mixed> $pageFilters
 *
 * @return array<string, int>
 */
function cumulativeStudentsByMonth(array $pageFilters = ['startDate' => '2026-04-01', 'endDate' => '2026-06-30']): array
{
    $widget = new StudentCumulativeCountLineChart();
    $widget->cacheTag = 'report-student';
    $widget->pageFilters = $pageFilters;

    $data = $widget->getData();

    return array_combine($data['labels'], $data['datasets'][0]['data']);
}

it('returns correct cumulative student counts grouped by month within the given date range', function () {
    Student::factory()->count(5)->state(['created_at_source' => '2026-03-10'])->create();
    Student::factory()->count(5)->state(['created_at_source' => '2026-06-25'])->create();

    expect(cumulativeStudentsByMonth(['startDate' => '2026-03-01', 'endDate' => '2026-06-30']))->toBe([
        'Mar 2026' => 5,
        'Apr 2026' => 5,
        'May 2026' => 5,
        'Jun 2026' => 10,
    ]);
});

it('returns correct cumulative student counts grouped by month based on group filters', function () {
    // With no date filter the chart defaults to the twelve months ending now, so "now" has
    // to be pinned for the series to be predictable.
    Carbon::setTestNow('2026-06-15');

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

    Student::factory()->count(5)->state([
        'created_at_source' => '2026-03-15',
        'last' => 'John',
    ])->create();

    Student::factory()->count(5)->state([
        'created_at_source' => '2026-06-10',
        'last' => 'Doe',
    ])->create();

    $emptyMonths = array_fill_keys(
        ['Jul 2025', 'Aug 2025', 'Sep 2025', 'Oct 2025', 'Nov 2025', 'Dec 2025', 'Jan 2026', 'Feb 2026'],
        0,
    );

    // With the group filter only the Johns count.
    expect(cumulativeStudentsByMonth(['populationGroup' => $group->getKey()]))->toBe([
        ...$emptyMonths,
        'Mar 2026' => 5,
        'Apr 2026' => 5,
        'May 2026' => 5,
        'Jun 2026' => 5,
    ]);

    // Without any filter the Does join in June.
    expect(cumulativeStudentsByMonth([]))->toBe([
        ...$emptyMonths,
        'Mar 2026' => 5,
        'Apr 2026' => 5,
        'May 2026' => 5,
        'Jun 2026' => 10,
    ]);
});

describe('archiving', function () {
    it('drops an archived student from the month they were archived onwards', function () {
        Student::factory()->count(2)->state(['created_at_source' => '2026-04-10'])->create();

        $archived = Student::factory()->create(['created_at_source' => '2026-04-10']);
        $archived->forceFill(['archived_at' => Carbon::parse('2026-05-20')])->save();

        expect(cumulativeStudentsByMonth())->toBe([
            'Apr 2026' => 3,
            'May 2026' => 2,
            'Jun 2026' => 2,
        ]);
    });

    it('keeps counting a student archived after the reported period', function () {
        $archived = Student::factory()->create(['created_at_source' => '2026-04-10']);
        $archived->forceFill(['archived_at' => Carbon::parse('2026-08-20')])->save();

        expect(cumulativeStudentsByMonth())->toBe([
            'Apr 2026' => 1,
            'May 2026' => 1,
            'Jun 2026' => 1,
        ]);
    });

    // Subtracting an archived student who was never added — because they were created before
    // the window the chart is showing — would drive the running total below the students it
    // actually counted, and the axis minimum would hide it rather than fix it.
    it('does not subtract a student created before the reported period', function () {
        $archived = Student::factory()->create(['created_at_source' => '2026-01-10']);
        $archived->forceFill(['archived_at' => Carbon::parse('2026-05-20')])->save();

        expect(cumulativeStudentsByMonth())->toBe([
            'Apr 2026' => 0,
            'May 2026' => 0,
            'Jun 2026' => 0,
        ]);
    });

    it('does not subtract archived students while the feature is inactive', function () {
        StudentArchivingFeature::deactivate();

        $archived = Student::factory()->create(['created_at_source' => '2026-04-10']);
        $archived->forceFill(['archived_at' => Carbon::parse('2026-05-20')])->save();

        expect(cumulativeStudentsByMonth())->toBe([
            'Apr 2026' => 1,
            'May 2026' => 1,
            'Jun 2026' => 1,
        ]);
    });
});
