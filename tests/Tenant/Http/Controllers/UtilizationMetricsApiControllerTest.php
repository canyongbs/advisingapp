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

use AdvisingApp\Alert\Actions\GenerateStudentAlertsView;
use AdvisingApp\Alert\Configurations\AdultLearnerAlertConfiguration;
use AdvisingApp\Alert\Models\AlertConfiguration;
use AdvisingApp\Alert\Presets\AlertPreset;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Http\Controllers\UtilizationMetricsApiController;
use Illuminate\Http\Request;

use function Pest\Laravel\getJson;

/**
 * The metrics themselves are asserted by invoking the controller directly rather than over
 * HTTP. The route sits behind `CheckOlympusKey`, which calls `Landlord::execute()` before
 * the controller runs; that re-establishes the tenant connection and rolls back the open
 * `RefreshDatabase` transaction, so every metric would come back as zero. The middleware is
 * covered separately by the authorization test below, which needs no tenant data.
 *
 * @return array<string, mixed>
 */
function utilizationMetrics(): array
{
    $response = app(UtilizationMetricsApiController::class)(Request::create('/', 'GET'));

    return $response->getData(true)['data'];
}

it('does not count archived students in the reported student records', function () {
    Student::truncate();

    Student::factory()->count(3)->create();

    expect(utilizationMetrics()['student_records'])->toBe(3);

    $archived = Student::factory()->count(2)->create();
    $archived->each(fn (Student $student) => $student->archive());

    expect(Student::query()->count())->toBe(5)
        ->and(utilizationMetrics()['student_records'])->toBe(3);
});

it('does not count alerts belonging to archived students', function () {
    Student::truncate();

    $minimumAge = 25;

    $configuration = AdultLearnerAlertConfiguration::factory()
        ->state(['minimum_age' => $minimumAge])
        ->create();

    AlertConfiguration::factory()
        ->state([
            'preset' => AlertPreset::AdultLearner,
            'configuration_id' => $configuration->id,
            'configuration_type' => $configuration->getMorphClass(),
        ])
        ->enabled()
        ->create();

    $birthdate = (now()->year - $minimumAge - 1) . '-01-01';

    Student::factory()->count(3)->state(['birthdate' => $birthdate])->create();

    $archived = Student::factory()->count(2)->state(['birthdate' => $birthdate])->create();

    app(GenerateStudentAlertsView::class)->execute();

    expect(utilizationMetrics()['alerts_by_alert_type'][AlertPreset::AdultLearner->value])->toBe(5);

    $archived->each(fn (Student $student) => $student->archive());

    expect(utilizationMetrics()['alerts_by_alert_type'][AlertPreset::AdultLearner->value])->toBe(3);
});

it('still reports alert types that have no alerts', function () {
    Student::truncate();

    expect(utilizationMetrics()['alerts_by_alert_type'])
        ->toHaveKey(AlertPreset::AdultLearner->value)
        ->and(utilizationMetrics()['alerts_by_alert_type'][AlertPreset::AdultLearner->value])->toBe(0);
});

describe('authorization', function () {
    it('denies access without a valid Olympus key', function () {
        getJson(route('utilization-metrics', [], false))
            ->assertForbidden();
    });
});
