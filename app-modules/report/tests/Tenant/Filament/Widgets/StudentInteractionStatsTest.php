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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Report\Filament\Pages\StudentInteractionReport;
use AdvisingApp\Report\Filament\Widgets\StudentInteractionStats;
use AdvisingApp\StudentDataModel\Models\Student;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

/** @var array<LicenseType> $licenses */
$licenses = [
    LicenseType::RetentionCrm,
];
$permission = [
    'report-library.view-any',
];

it('cannot render without a license', function () use ($permission) {
    actingAs(user(
        permissions: $permission
    ));

    get(StudentInteractionReport::getUrl())
        ->assertForbidden();
});

it('cannot render without permissions', function () use ($licenses) {
    actingAs(user(
        licenses: $licenses
    ));

    get(StudentInteractionReport::getUrl())
        ->assertForbidden();
});

it('can render', function () use ($licenses, $permission) {
    actingAs(user(
        licenses: $licenses,
        permissions: $permission
    ));

    get(StudentInteractionReport::getUrl())
        ->assertSuccessful();
});

it('Check total interactions', function () {
    $interactionCount = rand(1, 10);
    $studentInteractionStats = new StudentInteractionStats();
    $studentInteractionStats->cacheTag = 'report-student-interaction';

    Student::factory()
        ->has(Interaction::factory()->count($interactionCount), 'interactions')
        ->create();

    $stats = $studentInteractionStats->getStats();
    $totalStudentInteractionsStat = $stats[0];
    expect($totalStudentInteractionsStat->getValue())->toEqual($interactionCount);
});

it('Check unique students with interactions', function () {
    $interactionCount = rand(1, 10);
    $studentInteractionStats = new StudentInteractionStats();
    $studentInteractionStats->cacheTag = 'report-student-interaction';

    Student::factory()
        ->count($interactionCount)
        ->has(Interaction::factory()->count(1), 'interactions')
        ->create();

    $stats = $studentInteractionStats->getStats();
    $totaluniqueStudentInteractionsStat = $stats[1];
    expect($totaluniqueStudentInteractionsStat->getValue())->toEqual($interactionCount);
});

it('returns correct total and unique student interaction counts within the given date range', function () {
    $studentsWithStartDateInteractions = random_int(1, 10);
    $studentsWithEndDateInteractions = random_int(1, 10);
    $interactionStartDate = now()->subDays(10);
    $interactionEndDate = now()->subDays(5);

    Student::factory()->count($studentsWithStartDateInteractions)
        ->has(
            Interaction::factory()->state([
                'created_at' => $interactionStartDate,
            ]),
            'interactions'
        )->create();

    Student::factory()->count($studentsWithEndDateInteractions)
        ->has(
            Interaction::factory()->state([
                'created_at' => $interactionEndDate,
            ]),
            'interactions'
        )->create();

    Student::factory()->count($studentsWithEndDateInteractions)
        ->has(
            Interaction::factory()->count(2)->state([
                'created_at' => $interactionStartDate,
            ]),
            'interactions'
        )->create();

    $widget = new StudentInteractionStats();
    $widget->cacheTag = 'report-student';
    $widget->filters = [
        'startDate' => $interactionStartDate->toDateString(),
        'endDate' => $interactionEndDate->toDateString(),
    ];

    $stats = $widget->getStats();

    $studentsTotalInteractionsStat = $stats[0];

    expect($studentsTotalInteractionsStat->getValue())
        ->toEqual($studentsWithStartDateInteractions + $studentsWithEndDateInteractions + ($studentsWithEndDateInteractions * 2));

    $studentsWithInteractionsStat = $stats[1];

    expect($studentsWithInteractionsStat->getValue())
        ->toEqual($studentsWithStartDateInteractions + $studentsWithEndDateInteractions + $studentsWithEndDateInteractions);
});
