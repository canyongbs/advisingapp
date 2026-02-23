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

use AdvisingApp\Alert\Actions\GenerateStudentAlertsView;
use AdvisingApp\Alert\Configurations\AdultLearnerAlertConfiguration;
use AdvisingApp\Alert\Enums\StudentAlertStatus;
use AdvisingApp\Alert\Models\AlertConfiguration;
use AdvisingApp\Alert\Presets\AlertPreset;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\ViewStudentAlerts;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('requires proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $student = Student::factory()->create();

    actingAs($user);

    livewire(ViewStudentAlerts::class, [
        'record' => $student->getKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('student.*.view');

    livewire(ViewStudentAlerts::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk();
});

it('renders active alerts as table rows', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $minimumAge = fake()->numberBetween(24, 30);

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

    $student = Student::factory()
        ->state(['birthdate' => ($currentYear - $minimumAge - 1) . '-01-01'])
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    livewire(ViewStudentAlerts::class, [
        'record' => $student->getKey(),
    ])
        ->assertCanSeeTableRecords([$alertConfig])
        ->assertTableColumnStateSet('alert_name', $alertConfig->preset->getHandler()->getName(), $alertConfig)
        ->assertTableColumnStateSet('alert_description', $alertConfig->preset->getHandler()->getDescription(), $alertConfig)
        ->assertTableColumnStateSet('alert_status', StudentAlertStatus::Active, $alertConfig)
        ->filterTable('status', StudentAlertStatus::Inactive)
        ->assertCanNotSeeTableRecords([$alertConfig])
        ->filterTable('status', null)
        ->assertCanSeeTableRecords([$alertConfig])
        ->filterTable('status', StudentAlertStatus::Active)
        ->assertCanSeeTableRecords([$alertConfig]);
});

it('renders inactive alerts as table rows', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $minimumAge = fake()->numberBetween(24, 30);

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

    $student = Student::factory()
        ->state(['birthdate' => ($currentYear - $minimumAge + 5) . '-01-01'])
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    livewire(ViewStudentAlerts::class, [
        'record' => $student->getKey(),
    ])
        ->assertCanNotSeeTableRecords([$alertConfig])
        ->filterTable('status', null)
        ->assertCanSeeTableRecords([$alertConfig])
        ->filterTable('status', StudentAlertStatus::Inactive)
        ->assertCanSeeTableRecords([$alertConfig])
        ->assertTableColumnStateSet('alert_name', $alertConfig->preset->getHandler()->getName(), $alertConfig)
        ->assertTableColumnStateSet('alert_description', $alertConfig->preset->getHandler()->getDescription(), $alertConfig)
        ->assertTableColumnStateSet('alert_status', StudentAlertStatus::Inactive, $alertConfig);
});

it('renders disabled alerts as table rows', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $minimumAge = fake()->numberBetween(24, 30);

    $config = AdultLearnerAlertConfiguration::factory()
        ->state(['minimum_age' => $minimumAge])
        ->create();

    $alertConfig = AlertConfiguration::factory()
        ->state([
            'preset' => AlertPreset::AdultLearner,
            'configuration_id' => $config->id,
            'configuration_type' => $config->getMorphClass(),
        ])
        ->disabled()
        ->create();

    $currentYear = now()->year;

    $student = Student::factory()
        ->state(['birthdate' => ($currentYear - $minimumAge + 5) . '-01-01'])
        ->create();

    app(GenerateStudentAlertsView::class)->execute();

    livewire(ViewStudentAlerts::class, [
        'record' => $student->getKey(),
    ])
        ->assertCanNotSeeTableRecords([$alertConfig])
        ->filterTable('status', null)
        ->assertCanSeeTableRecords([$alertConfig])
        ->filterTable('status', StudentAlertStatus::Disabled)
        ->assertCanSeeTableRecords([$alertConfig])
        ->assertCanSeeTableRecords([$alertConfig])
        ->assertTableColumnStateSet('alert_name', $alertConfig->preset->getHandler()->getName(), $alertConfig)
        ->assertTableColumnStateSet('alert_description', $alertConfig->preset->getHandler()->getDescription(), $alertConfig)
        ->assertTableColumnStateSet('alert_status', StudentAlertStatus::Disabled, $alertConfig);
});
