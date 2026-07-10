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

use AdvisingApp\Authorization\Filament\Pages\Reporting;
use AdvisingApp\Report\Enums\ReportAccessKey;
use AdvisingApp\Report\Models\ReportDepartmentAccess;
use AdvisingApp\Report\Models\ReportUserAccess;
use AdvisingApp\Team\Models\Department;
use App\Features\ReportingFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(function () {
    ReportingFeature::activate();
});

it('cannot be accessed by a user without the reporting permission', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(Reporting::getUrl())->assertForbidden();
});

it('can be accessed by a user with the reporting permission', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('reporting.view-any');

    actingAs($user);

    get(Reporting::getUrl())->assertSuccessful();
});

it('always lists reports that require no license or addon', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->limits->conversationalAiSeats = 0;
    $settings->data->limits->retentionCrmSeats = 0;
    $settings->data->limits->recruitmentCrmSeats = 0;
    $settings->data->addons->caseManagement = false;
    $settings->data->addons->customerAdvisors = false;
    $settings->data->addons->employeeAdvisors = false;
    $settings->data->addons->researchAdvisor = false;
    $settings->data->addons->projectManagement = false;
    $settings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');

    actingAs($user);

    livewire(Reporting::class)
        ->assertCanSeeTableRecords([ReportAccessKey::UserLoginActivity->value]);
});

it('only lists a report when the required licenses and addons are enabled for the tenant', function (Closure $enableReport, ReportAccessKey $case) {
    $settings = app(LicenseSettings::class);
    $settings->data->limits->conversationalAiSeats = 0;
    $settings->data->limits->retentionCrmSeats = 0;
    $settings->data->limits->recruitmentCrmSeats = 0;
    $settings->data->addons->caseManagement = false;
    $settings->data->addons->customerAdvisors = false;
    $settings->data->addons->employeeAdvisors = false;
    $settings->data->addons->researchAdvisor = false;
    $settings->data->addons->projectManagement = false;
    $settings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');

    actingAs($user);

    livewire(Reporting::class)
        ->assertCanNotSeeTableRecords([$case->value]);

    $enableReport($settings);
    $settings->save();

    livewire(Reporting::class)
        ->assertCanSeeTableRecords([$case->value]);
})->with([
    ReportAccessKey::ArtificialIntelligence->value => [
        fn (LicenseSettings $settings) => $settings->data->limits->conversationalAiSeats = 10,
        ReportAccessKey::ArtificialIntelligence,
    ],
    ReportAccessKey::CustomerAdvisorReport->value => [
        function (LicenseSettings $settings) {
            $settings->data->limits->conversationalAiSeats = 10;
            $settings->data->addons->customerAdvisors = true;
        },
        ReportAccessKey::CustomerAdvisorReport,
    ],
    ReportAccessKey::EmployeeAdvisorReport->value => [
        function (LicenseSettings $settings) {
            $settings->data->limits->conversationalAiSeats = 10;
            $settings->data->addons->employeeAdvisors = true;
        },
        ReportAccessKey::EmployeeAdvisorReport,
    ],
    ReportAccessKey::InstitutionalAdvisorReport->value => [
        fn (LicenseSettings $settings) => $settings->data->limits->conversationalAiSeats = 10,
        ReportAccessKey::InstitutionalAdvisorReport,
    ],
    ReportAccessKey::ResearchAdvisorReport->value => [
        function (LicenseSettings $settings) {
            $settings->data->limits->conversationalAiSeats = 10;
            $settings->data->addons->researchAdvisor = true;
        },
        ReportAccessKey::ResearchAdvisorReport,
    ],
    ReportAccessKey::ProjectReport->value => [
        fn (LicenseSettings $settings) => $settings->data->addons->projectManagement = true,
        ReportAccessKey::ProjectReport,
    ],
    ReportAccessKey::StudentActionCenter->value => [
        fn (LicenseSettings $settings) => $settings->data->limits->retentionCrmSeats = 10,
        ReportAccessKey::StudentActionCenter,
    ],
    ReportAccessKey::Students->value => [
        fn (LicenseSettings $settings) => $settings->data->limits->retentionCrmSeats = 10,
        ReportAccessKey::Students,
    ],
    ReportAccessKey::StudentCaseReport->value => [
        function (LicenseSettings $settings) {
            $settings->data->limits->retentionCrmSeats = 10;
            $settings->data->addons->caseManagement = true;
        },
        ReportAccessKey::StudentCaseReport,
    ],
    ReportAccessKey::StudentDeliverabilityReport->value => [
        fn (LicenseSettings $settings) => $settings->data->limits->retentionCrmSeats = 10,
        ReportAccessKey::StudentDeliverabilityReport,
    ],
    ReportAccessKey::StudentInteractionReport->value => [
        fn (LicenseSettings $settings) => $settings->data->limits->retentionCrmSeats = 10,
        ReportAccessKey::StudentInteractionReport,
    ],
    ReportAccessKey::StudentMessagesDetailReport->value => [
        fn (LicenseSettings $settings) => $settings->data->limits->retentionCrmSeats = 10,
        ReportAccessKey::StudentMessagesDetailReport,
    ],
    ReportAccessKey::StudentMessagesOverviewReport->value => [
        fn (LicenseSettings $settings) => $settings->data->limits->retentionCrmSeats = 10,
        ReportAccessKey::StudentMessagesOverviewReport,
    ],
    ReportAccessKey::StudentTaskManagement->value => [
        fn (LicenseSettings $settings) => $settings->data->limits->retentionCrmSeats = 10,
        ReportAccessKey::StudentTaskManagement,
    ],
    ReportAccessKey::ProspectActionCenter->value => [
        fn (LicenseSettings $settings) => $settings->data->limits->recruitmentCrmSeats = 10,
        ReportAccessKey::ProspectActionCenter,
    ],
    ReportAccessKey::ProspectReport->value => [
        fn (LicenseSettings $settings) => $settings->data->limits->recruitmentCrmSeats = 10,
        ReportAccessKey::ProspectReport,
    ],
    ReportAccessKey::ProspectCaseReport->value => [
        function (LicenseSettings $settings) {
            $settings->data->limits->recruitmentCrmSeats = 10;
            $settings->data->addons->caseManagement = true;
        },
        ReportAccessKey::ProspectCaseReport,
    ],
    ReportAccessKey::ProspectInteractionReport->value => [
        fn (LicenseSettings $settings) => $settings->data->limits->recruitmentCrmSeats = 10,
        ReportAccessKey::ProspectInteractionReport,
    ],
    ReportAccessKey::ProspectMessagesDetailReport->value => [
        fn (LicenseSettings $settings) => $settings->data->limits->recruitmentCrmSeats = 10,
        ReportAccessKey::ProspectMessagesDetailReport,
    ],
    ReportAccessKey::ProspectMessagesOverviewReport->value => [
        fn (LicenseSettings $settings) => $settings->data->limits->recruitmentCrmSeats = 10,
        ReportAccessKey::ProspectMessagesOverviewReport,
    ],
    ReportAccessKey::ProspectTaskManagement->value => [
        fn (LicenseSettings $settings) => $settings->data->limits->recruitmentCrmSeats = 10,
        ReportAccessKey::ProspectTaskManagement,
    ],
]);

it('can search reports by name', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->projectManagement = true;
    $settings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');

    actingAs($user);

    livewire(Reporting::class)
        ->searchTable(ReportAccessKey::ProjectReport->getName())
        ->assertCanSeeTableRecords([ReportAccessKey::ProjectReport->value])
        ->assertCanNotSeeTableRecords([ReportAccessKey::UserLoginActivity->value]);
});

it('can filter reports by category', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');

    actingAs($user);

    livewire(Reporting::class)
        ->filterTable('category', ReportAccessKey::UserLoginActivity->getCategory())
        ->assertCanSeeTableRecords([ReportAccessKey::UserLoginActivity->value])
        ->assertCanNotSeeTableRecords([ReportAccessKey::ArtificialIntelligence->value]);
});

it('manage report action with approprite permissions', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');
    $user->givePermissionTo('reporting.*.update');

    actingAs($user);

    livewire(Reporting::class)
        ->assertActionVisible(TestAction::make('manage')->table(ReportAccessKey::UserLoginActivity->value));
});

it('can not manage report action without approprite permissions', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');

    actingAs($user);

    livewire(Reporting::class)
        ->assertActionHidden(TestAction::make('manage')->table(ReportAccessKey::UserLoginActivity->value));
});

it('assigns users to a report through the manage action', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');
    $user->givePermissionTo('reporting.*.update');

    $assignedUser = User::factory()->create();

    actingAs($user);

    livewire(Reporting::class)
        ->callAction(TestAction::make('manage')->table(ReportAccessKey::UserLoginActivity->value), [
            'users' => [$assignedUser->getKey()],
            'departments' => [],
        ])
        ->assertNotified();

    expect(
        ReportUserAccess::query()
            ->where('report_key', ReportAccessKey::UserLoginActivity->value)
            ->where('user_id', $assignedUser->getKey())
            ->exists()
    )->toBeTrue();
});

it('assigns departments to a report through the manage action', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');
    $user->givePermissionTo('reporting.*.update');

    $department = Department::factory()->create();

    actingAs($user);

    livewire(Reporting::class)
        ->callAction(TestAction::make('manage')->table(ReportAccessKey::UserLoginActivity->value), [
            'users' => [],
            'departments' => [$department->getKey()],
        ])
        ->assertNotified();

    expect(
        ReportDepartmentAccess::query()
            ->where('report_key', ReportAccessKey::UserLoginActivity->value)
            ->where('team_id', $department->getKey())
            ->exists()
    )->toBeTrue();
});

it('removes access that is no longer selected when managing a report', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');
    $user->givePermissionTo('reporting.*.update');

    $previouslyAssignedUser = User::factory()->create();

    ReportUserAccess::factory()->create([
        'report_key' => ReportAccessKey::UserLoginActivity->value,
        'user_id' => $previouslyAssignedUser->getKey(),
    ]);

    actingAs($user);

    livewire(Reporting::class)
        ->callAction(TestAction::make('manage')->table(ReportAccessKey::UserLoginActivity->value), [
            'users' => [],
            'departments' => [],
        ])
        ->assertNotified();

    expect(
        ReportUserAccess::query()
            ->where('report_key', ReportAccessKey::UserLoginActivity->value)
            ->where('user_id', $previouslyAssignedUser->getKey())
            ->exists()
    )->toBeFalse();
});

it('counts a user with both direct and department access only once', function () {
    $department = Department::factory()->create();

    $user = User::factory()->create(['team_id' => $department->getKey()]);

    ReportUserAccess::factory()->create([
        'report_key' => ReportAccessKey::UserLoginActivity->value,
        'user_id' => $user->getKey(),
    ]);

    ReportDepartmentAccess::factory()->create([
        'report_key' => ReportAccessKey::UserLoginActivity->value,
        'team_id' => $department->getKey(),
    ]);

    expect(ReportAccessKey::UserLoginActivity->accessCount())->toEqual(1);
});
