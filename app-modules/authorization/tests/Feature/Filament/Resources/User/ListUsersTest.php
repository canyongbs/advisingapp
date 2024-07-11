<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;

use function Tests\asSuperAdmin;

use AdvisingApp\Team\Models\Team;

use function Pest\Laravel\actingAs;

use Illuminate\Support\Facades\Log;

use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertFalse;

use AdvisingApp\Authorization\Enums\LicenseType;
use Lab404\Impersonate\Services\ImpersonateManager;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Actions\AssignLicensesBulkAction;

it('renders impersonate button for non super admin users when user is super admin', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $component = livewire(ListUsers::class);

    $component
        ->assertSuccessful()
        ->assertCountTableRecords(2)
        ->assertTableActionVisible(Impersonate::class, $user);
});

it('does not render impersonate button for super admin users when user is not super admin', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()
        ->create()
        ->givePermissionTo('user.view-any', 'user.*.view');
    actingAs($user);

    $component = livewire(ListUsers::class);

    $component
        ->assertSuccessful()
        ->assertCountTableRecords(1)
        ->assertTableActionHidden(Impersonate::class, $superAdmin);
});

it('does not render impersonate button for super admin users at all', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()->create();
    asSuperAdmin($user);

    $component = livewire(ListUsers::class);

    $component
        ->assertSuccessful()
        ->assertCountTableRecords(2)
        ->assertTableActionHidden(Impersonate::class, $superAdmin);
});

it('does not render impersonate button for super admin users even if user has permission', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()
        ->create()
        ->givePermissionTo('user.view-any', 'user.*.view', 'authorization.impersonate');

    actingAs($user);

    $component = livewire(ListUsers::class);

    $component
        ->assertSuccessful()
        ->assertCountTableRecords(1)
        ->assertTableActionHidden(Impersonate::class, $superAdmin);
});

it('allows super admin user to impersonate', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()->create();

    $component = livewire(ListUsers::class);

    $component
        ->assertSuccessful()
        ->assertCountTableRecords(2)
        ->callTableAction(Impersonate::class, $user);

    expect($user->isImpersonated())->toBeTrue()
        ->and(auth()->id())->toBe($user->id);
});

it('allows user with permission to impersonate', function () {
    $first = User::factory()->create();
    $first->givePermissionTo('user.view-any', 'user.*.view', 'authorization.impersonate');
    actingAs($first);

    $second = User::factory()->create();

    $component = livewire(ListUsers::class);

    $component
        ->assertSuccessful()
        ->assertCountTableRecords(2)
        ->callTableAction(Impersonate::class, $second);

    expect($second->isImpersonated())->toBeTrue()
        ->and(auth()->id())->toBe($second->id);
});

it('allows a user to leave impersonate', function () {
    $first = User::factory()->create();
    $first->givePermissionTo('authorization.impersonate');
    actingAs($first);

    $second = User::factory()->create();

    app(ImpersonateManager::class)->take($first, $second);

    expect($second->isImpersonated())->toBeTrue()
        ->and(auth()->id())->toBe($second->id);

    $second->leaveImpersonation();

    expect($second->isImpersonated())->toBeFalse()
        ->and(auth()->id())->toBe($first->id);
});

it('does not allow a user without permission to assign licenses in bulk', function () {
    $user = User::factory()->create();
    $user->givePermissionTo([
        'user.view-any',
        'user.create',
        'user.*.update',
        'user.*.view',
        'user.*.delete',
        'user.*.restore',
        'user.*.force-delete',
    ]);
    actingAs($user);

    $records = User::factory(2)->create()->prepend($user);

    livewire(ListUsers::class)
        ->assertSuccessful()
        ->assertCountTableRecords($records->count())
        ->assertTableBulkActionHidden(AssignLicensesBulkAction::class);
});

it('allows a user with permission to assign licenses in bulk', function () {
    $user = User::factory()->create();
    $user->givePermissionTo([
        'user.view-any',
        'user.create',
        'user.*.update',
        'user.*.view',
        'user.*.delete',
        'user.*.restore',
        'user.*.force-delete',
        'license.view-any',
        'license.create',
        'license.*.update',
        'license.*.view',
        'license.*.delete',
        'license.*.restore',
        'license.*.force-delete',
    ]);
    actingAs($user);

    $records = User::factory(2)->create()->prepend($user);

    $licenseTypes = collect(LicenseType::cases());

    $records->each(function (User $record) use ($licenseTypes) {
        $licenseTypes->each(fn ($license) => assertFalse($record->hasLicense($license)));
    });

    livewire(ListUsers::class)
        ->assertSuccessful()
        ->assertCountTableRecords($records->count())
        ->callTableBulkAction(AssignLicensesBulkAction::class, $records, [
            'replace' => true,
            ...$licenseTypes->mapWithKeys(fn (LicenseType $licenseType) => [$licenseType->value => true]),
        ])
        ->assertHasNoTableBulkActionErrors()
        ->assertNotified('Assigned Licenses');

    $records->each(function (User $record) use ($licenseTypes) {
        $record->refresh();
        $licenseTypes->each(fn (LicenseType $licenseType) => assertTrue($record->hasLicense($licenseType)));
    });
});

it('can filters users by multiple teams', function () {
    asSuperAdmin();

    $team1 = Team::factory()->create();

    $admins = User::factory()
        ->count(4)
        ->hasAttached($team1, [], 'teams')
        ->create();

    $team2 = Team::factory()->create();

    $mods = User::factory()
        ->count(4)
        ->hasAttached($team2, [], 'teams')
        ->create();

    $team3 = Team::factory()->create();

    $support = User::factory()
        ->count(4)
        ->hasAttached($team3, [], 'teams')
        ->create();

    Log::debug('New Logs');

    foreach ($admins as $a) {
        Log::debug($a->team);
    }
    Log::debug('-------------------');
    Log::debug($team1->id);
    Log::debug('--------------------------');

    livewire(ListUsers::class)
        ->assertCanSeeTableRecords($admins)
        ->filterTable('teams', [$team1->id, $team2->id])
        ->assertCanSeeTableRecords(
            $admins
        );
    // ->assertCanNotSeeTableRecords($support);
})->only();
