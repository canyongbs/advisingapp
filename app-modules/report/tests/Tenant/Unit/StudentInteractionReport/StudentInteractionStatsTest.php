<?php

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
