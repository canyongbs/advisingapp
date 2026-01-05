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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AdvisingApp\Alert\Actions\GenerateStudentAlertsView;
use AdvisingApp\Alert\Configurations\AdultLearnerAlertConfiguration;
use AdvisingApp\Alert\Configurations\NewStudentAlertConfiguration;
use AdvisingApp\Alert\Filament\Widgets\AlertStats;
use AdvisingApp\Alert\Models\AlertConfiguration;
use AdvisingApp\Alert\Presets\AlertPreset;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Student;

it('displays correct count for adult learner alerts', function () {
    $minimumAge = fake()->numberBetween(24, 30);
    $matchingCount = fake()->numberBetween(2, 5);
    $nonMatchingCount = fake()->numberBetween(1, 3);

    $config = AdultLearnerAlertConfiguration::factory()
        ->state(['minimum_age' => $minimumAge])
        ->create();

    $alertConfig = AlertConfiguration::factory()
        ->state([
            'preset' => AlertPreset::AdultLearner,
            'configuration_id' => $config->id,
            'configuration_type' => $config->getMorphClass(),
        ])
        ->enabled()
        ->create();

    $currentYear = now()->year;

    Student::factory()
        ->count($matchingCount)
        ->state(['birthdate' => ($currentYear - $minimumAge - 1) . '-01-01'])
        ->create();

    Student::factory()
        ->count($nonMatchingCount)
        ->state(['birthdate' => ($currentYear - $minimumAge + 5) . '-01-01'])
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    $widget = new AlertStats();
    $stats = $widget->getStats();

    expect($stats)->toHaveCount(1)
        ->and($stats[0]->getLabel())->toBe($alertConfig->preset->getLabel())
        ->and($stats[0]->getValue())->toBe((string) $matchingCount);
});

it('displays correct count for new student alerts', function () {
    $numberOfSemesters = fake()->numberBetween(1, 3);
    $matchingCount = fake()->numberBetween(2, 5);
    $nonMatchingCount = fake()->numberBetween(1, 3);

    $config = NewStudentAlertConfiguration::factory()
        ->state(['number_of_semesters' => $numberOfSemesters])
        ->create();

    $alertConfig = AlertConfiguration::factory()
        ->state([
            'preset' => AlertPreset::NewStudent,
            'configuration_id' => $config->id,
            'configuration_type' => $config->getMorphClass(),
        ])
        ->enabled()
        ->create();

    Student::factory()
        ->count($matchingCount)
        ->has(
            Enrollment::factory()
                ->count($numberOfSemesters)
                ->sequence(fn ($sequence) => ['semester_code' => '420' . ($sequence->index + 1)]),
            'enrollments'
        )
        ->create();

    Student::factory()
        ->count($nonMatchingCount)
        ->has(
            Enrollment::factory()
                ->count($numberOfSemesters + 2)
                ->sequence(fn ($sequence) => ['semester_code' => '420' . ($sequence->index + 1)]),
            'enrollments'
        )
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    $widget = new AlertStats();
    $stats = $widget->getStats();

    expect($stats)->toHaveCount(1)
        ->and($stats[0]->getLabel())->toBe($alertConfig->preset->getLabel())
        ->and($stats[0]->getValue())->toBe((string) $matchingCount);
});

it('displays correct count for first generation student alerts', function () {
    $firstGenCount = fake()->numberBetween(2, 5);
    $nonFirstGenCount = fake()->numberBetween(1, 3);

    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::FirstGenerationStudent])
        ->enabled()
        ->create();

    Student::factory()
        ->count($firstGenCount)
        ->state(['firstgen' => true])
        ->create();

    Student::factory()
        ->count($nonFirstGenCount)
        ->state(['firstgen' => false])
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    $widget = new AlertStats();
    $stats = $widget->getStats();

    expect($stats)->toHaveCount(1)
        ->and($stats[0]->getLabel())->toBe($alertConfig->preset->getLabel())
        ->and($stats[0]->getValue())->toBe((string) $firstGenCount);
});

it('displays correct count for D or F grade alerts', function () {
    $matchingCount = fake()->numberBetween(2, 5);
    $nonMatchingCount = fake()->numberBetween(1, 3);

    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->enabled()
        ->create();

    Student::factory()
        ->count($matchingCount)
        ->has(
            Enrollment::factory()->state(['crse_grade_off' => fake()->randomElement(['D', 'F'])]),
            'enrollments'
        )
        ->create();

    Student::factory()
        ->count($nonMatchingCount)
        ->has(
            Enrollment::factory()->state(['crse_grade_off' => 'A']),
            'enrollments'
        )
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    $widget = new AlertStats();
    $stats = $widget->getStats();

    expect($stats)->toHaveCount(1)
        ->and($stats[0]->getLabel())->toBe($alertConfig->preset->getLabel())
        ->and($stats[0]->getValue())->toBe((string) $matchingCount);
});

it('displays correct count for multiple D or F grades alerts', function () {
    $matchingCount = fake()->numberBetween(2, 4);
    $nonMatchingCount = fake()->numberBetween(1, 3);

    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::MultipleDorfGrades])
        ->enabled()
        ->create();

    Student::factory()
        ->count($matchingCount)
        ->has(
            Enrollment::factory()
                ->count(2)
                ->sequence(
                    ['crse_grade_off' => 'D', 'class_nbr' => '10001'],
                    ['crse_grade_off' => 'F', 'class_nbr' => '10002']
                ),
            'enrollments'
        )
        ->create();

    Student::factory()
        ->count($nonMatchingCount)
        ->has(
            Enrollment::factory()->state(['crse_grade_off' => 'D']),
            'enrollments'
        )
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    $widget = new AlertStats();
    $stats = $widget->getStats();

    expect($stats)->toHaveCount(1)
        ->and($stats[0]->getLabel())->toBe($alertConfig->preset->getLabel())
        ->and($stats[0]->getValue())->toBe((string) $matchingCount);
});

it('displays correct count for course withdrawal alerts', function () {
    $matchingCount = fake()->numberBetween(2, 5);
    $nonMatchingCount = fake()->numberBetween(1, 3);

    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::CourseWithdrawal])
        ->enabled()
        ->create();

    Student::factory()
        ->count($matchingCount)
        ->has(
            Enrollment::factory()->state(['crse_grade_off' => 'W']),
            'enrollments'
        )
        ->create();

    Student::factory()
        ->count($nonMatchingCount)
        ->has(
            Enrollment::factory()->state(['crse_grade_off' => 'A']),
            'enrollments'
        )
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    $widget = new AlertStats();
    $stats = $widget->getStats();

    expect($stats)->toHaveCount(1)
        ->and($stats[0]->getLabel())->toBe($alertConfig->preset->getLabel())
        ->and($stats[0]->getValue())->toBe((string) $matchingCount);
});

it('displays correct count for multiple course withdrawals alerts', function () {
    $matchingCount = fake()->numberBetween(2, 4);
    $nonMatchingCount = fake()->numberBetween(1, 3);

    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::MultipleCourseWithdrawals])
        ->enabled()
        ->create();

    Student::factory()
        ->count($matchingCount)
        ->has(
            Enrollment::factory()
                ->count(2)
                ->sequence(
                    ['crse_grade_off' => 'W', 'class_nbr' => '10001'],
                    ['crse_grade_off' => 'W', 'class_nbr' => '10002']
                ),
            'enrollments'
        )
        ->create();

    Student::factory()
        ->count($nonMatchingCount)
        ->has(
            Enrollment::factory()->state(['crse_grade_off' => 'W']),
            'enrollments'
        )
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    $widget = new AlertStats();
    $stats = $widget->getStats();

    expect($stats)->toHaveCount(1)
        ->and($stats[0]->getLabel())->toBe($alertConfig->preset->getLabel())
        ->and($stats[0]->getValue())->toBe((string) $matchingCount);
});

it('displays correct count for repeated course attempt alerts', function () {
    $matchingCount = fake()->numberBetween(2, 4);
    $nonMatchingCount = fake()->numberBetween(1, 3);

    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::RepeatedCourseAttempt])
        ->enabled()
        ->create();

    Student::factory()
        ->count($matchingCount)
        ->has(
            Enrollment::factory()
                ->count(2)
                ->sequence(
                    ['class_nbr' => '10001', 'crse_grade_off' => 'D'],
                    ['class_nbr' => '10001', 'crse_grade_off' => 'C']
                ),
            'enrollments'
        )
        ->create();

    Student::factory()
        ->count($nonMatchingCount)
        ->has(
            Enrollment::factory()
                ->count(2)
                ->sequence(
                    ['class_nbr' => '10002'],
                    ['class_nbr' => '10003']
                ),
            'enrollments'
        )
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    $widget = new AlertStats();
    $stats = $widget->getStats();

    expect($stats)->toHaveCount(1)
        ->and($stats[0]->getLabel())->toBe($alertConfig->preset->getLabel())
        ->and($stats[0]->getValue())->toBe((string) $matchingCount);
});

it('shows empty state when no alerts are configured', function () {
    $widget = new AlertStats();
    $stats = $widget->getStats();

    expect($stats)->toHaveCount(1)
        ->and($stats[0]->getLabel())->toBe('No Alerts Configured')
        ->and($stats[0]->getValue())->toBe('Please configure alert presets to see statistics.');
});

it('only shows enabled alert configurations', function () {
    AlertConfiguration::factory()
        ->has(AdultLearnerAlertConfiguration::factory(), 'configuration')
        ->state([
            'preset' => AlertPreset::AdultLearner,
            'is_enabled' => false,
        ])
        ->create();

    $widget = new AlertStats();
    $stats = $widget->getStats();

    expect($stats)->toHaveCount(1)
        ->and($stats[0]->getLabel())->toBe('No Alerts Configured');
});

it('filters alerts by population group', function () {
    $inGroupCount = fake()->numberBetween(2, 4);
    $outGroupCount = fake()->numberBetween(2, 4);

    $group = Group::factory()
        ->state([
            'model' => GroupModel::Student,
            'filters' => [
                'queryBuilder' => [
                    'rules' => [
                        'C0Cy' => [
                            'type' => 'last',
                            'data' => [
                                'operator' => 'contains',
                                'settings' => [
                                    'text' => 'Smith',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::FirstGenerationStudent])
        ->enabled()
        ->create();

    Student::factory()
        ->count($inGroupCount)
        ->state(['last' => 'Smith', 'firstgen' => true])
        ->create();

    Student::factory()
        ->count($outGroupCount)
        ->state(['last' => 'Jones', 'firstgen' => true])
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    $widget = new AlertStats();
    $widget->pageFilters = ['populationGroup' => $group->getKey()];
    $stats = $widget->getStats();

    expect($stats)->toHaveCount(1)
        ->and($stats[0]->getLabel())->toBe($alertConfig->preset->getLabel())
        ->and($stats[0]->getValue())->toBe((string) $inGroupCount);
});
