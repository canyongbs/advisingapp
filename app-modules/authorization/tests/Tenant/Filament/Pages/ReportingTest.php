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
use AdvisingApp\Report\Models\ReportTeamAccess;
use AdvisingApp\Report\Models\ReportUserAccess;
use AdvisingApp\Report\Support\ReportAccess;
use AdvisingApp\Team\Models\Team;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

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

it('only lists reports that are available to the tenant', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->limits->retentionCrmSeats = 0;
    $settings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');

    actingAs($user);

    livewire(Reporting::class)
        ->assertSee(ReportAccessKey::UserLoginActivity->getName())
        ->assertDontSee(ReportAccessKey::Students->getName());

    $settings->data->limits->retentionCrmSeats = 10;
    $settings->save();

    livewire(Reporting::class)
        ->assertSee(ReportAccessKey::Students->getName());
});

it('can search reports by name', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->projectManagement = true;
    $settings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');

    actingAs($user);

    livewire(Reporting::class)
        ->searchTable(ReportAccessKey::ProjectReport->getName())
        ->assertSee(ReportAccessKey::ProjectReport->getName())
        ->assertDontSee(ReportAccessKey::UserLoginActivity->getName());
});

it('can filter reports by category', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');

    actingAs($user);

    livewire(Reporting::class)
        ->filterTable('category', ReportAccessKey::UserLoginActivity->getCategory())
        ->assertSee(ReportAccessKey::UserLoginActivity->getName())
        ->assertDontSee(ReportAccessKey::ArtificialIntelligence->getName());
});

it('assigns users to a report through the manage action', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');

    $assignedUser = User::factory()->create();

    actingAs($user);

    livewire(Reporting::class)
        ->callTableAction('manage', record: ReportAccessKey::UserLoginActivity->value, data: [
            'users' => [$assignedUser->getKey()],
            'teams' => [],
        ])
        ->assertHasNoTableActionErrors();

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

    $team = Team::factory()->create();

    actingAs($user);

    livewire(Reporting::class)
        ->callTableAction('manage', record: ReportAccessKey::UserLoginActivity->value, data: [
            'users' => [],
            'teams' => [$team->getKey()],
        ])
        ->assertHasNoTableActionErrors();

    expect(
        ReportTeamAccess::query()
            ->where('report_key', ReportAccessKey::UserLoginActivity->value)
            ->where('team_id', $team->getKey())
            ->exists()
    )->toBeTrue();
});

it('removes access that is no longer selected when managing a report', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');

    $previouslyAssignedUser = User::factory()->create();

    ReportUserAccess::factory()->create([
        'report_key' => ReportAccessKey::UserLoginActivity->value,
        'user_id' => $previouslyAssignedUser->getKey(),
    ]);

    actingAs($user);

    livewire(Reporting::class)
        ->callTableAction('manage', record: ReportAccessKey::UserLoginActivity->value, data: [
            'users' => [],
            'teams' => [],
        ])
        ->assertHasNoTableActionErrors();

    expect(
        ReportUserAccess::query()
            ->where('report_key', ReportAccessKey::UserLoginActivity->value)
            ->where('user_id', $previouslyAssignedUser->getKey())
            ->exists()
    )->toBeFalse();
});

it('counts a user with both direct and team access only once', function () {
    $team = Team::factory()->create();

    $user = User::factory()->create(['team_id' => $team->getKey()]);

    ReportUserAccess::factory()->create([
        'report_key' => ReportAccessKey::UserLoginActivity->value,
        'user_id' => $user->getKey(),
    ]);

    ReportTeamAccess::factory()->create([
        'report_key' => ReportAccessKey::UserLoginActivity->value,
        'team_id' => $team->getKey(),
    ]);

    expect(ReportAccess::accessCount(ReportAccessKey::UserLoginActivity))->toEqual(1);
});
