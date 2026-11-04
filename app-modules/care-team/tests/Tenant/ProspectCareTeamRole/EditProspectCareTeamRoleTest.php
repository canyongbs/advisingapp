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

use AdvisingApp\CareTeam\Filament\Resources\ProspectCareTeamRoles\Pages\EditProspectCareTeamRole;
use AdvisingApp\CareTeam\Filament\Resources\ProspectCareTeamRoles\ProspectCareTeamRoleResource;
use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\CareTeam\Tests\Tenant\RequestFactories\EditCareTeamRoleRequestFactory;
use AdvisingApp\Prospect\Models\Prospect;
use App\Enums\CareTeamRoleType;
use App\Features\CareTeamRoleNameCitextFeature;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;
use function Tests\asSuperAdmin;

test('EditProspectCareTeamRole is gated with proper access control', function (): void {
    $user = User::factory()->create();

    $careTeamRole = CareTeamRole::factory()->create(['type' => CareTeamRoleType::Prospect]);

    actingAs($user)
        ->get(
            ProspectCareTeamRoleResource::getUrl('edit', [
                'record' => $careTeamRole,
            ])
        )->assertForbidden();

    Livewire::test(EditProspectCareTeamRole::class, [
        'record' => $careTeamRole->getRouteKey(),
    ])
        ->assertForbidden();

    $user->grantLicense(Prospect::getLicenseType());

    $user->refresh();

    actingAs($user)
        ->get(
            ProspectCareTeamRoleResource::getUrl('edit', [
                'record' => $careTeamRole,
            ])
        )->assertForbidden();

    Livewire::test(EditProspectCareTeamRole::class, [
        'record' => $careTeamRole->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.update');

    actingAs($user)
        ->get(
            ProspectCareTeamRoleResource::getUrl('edit', [
                'record' => $careTeamRole,
            ])
        )->assertSuccessful();

    Livewire::test(EditProspectCareTeamRole::class, [
        'record' => $careTeamRole->getRouteKey(),
    ])
        ->assertSuccessful();
});

test('A successful action on the EditProspectCareTeamRole page', function (): void {
    $careTeamRole = CareTeamRole::factory()->create(['type' => CareTeamRoleType::Prospect]);

    asSuperAdmin()
        ->get(
            ProspectCareTeamRoleResource::getUrl('edit', [
                'record' => $careTeamRole->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditCareTeamRoleRequestFactory::new()->state(['type' => CareTeamRoleType::Prospect])->create();

    Livewire::test(EditProspectCareTeamRole::class, [
        'record' => $careTeamRole->getRouteKey(),
    ])
        ->set('data', $editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $careTeamRole->fresh()->name);
});

test('EditProspectCareTeamRole requires valid data', function (EditCareTeamRoleRequestFactory $data, array $errors): void {
    asSuperAdmin();

    $careTeamRole = CareTeamRole::factory()->create(['type' => CareTeamRoleType::Prospect]);

    $editRequest = EditCareTeamRoleRequestFactory::new($data)->create();

    Livewire::test(EditProspectCareTeamRole::class, [
        'record' => $careTeamRole->getRouteKey(),
    ])
        ->set('data', $editRequest)
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(CareTeamRole::class, $careTeamRole->toArray());
})->with(
    [
        'name missing' => [EditCareTeamRoleRequestFactory::new()->state(['name' => null, 'type' => CareTeamRoleType::Prospect]), ['name' => 'required']],
        'name not a string' => [EditCareTeamRoleRequestFactory::new()->state(['name' => 1, 'type' => CareTeamRoleType::Prospect]), ['name' => 'string']],
        'is_default not a boolean' => [EditCareTeamRoleRequestFactory::new()->state(['is_default' => 'a', 'type' => CareTeamRoleType::Prospect]), ['is_default' => 'boolean']],
    ]
);

it('rejects renaming to a name matching another care team role case-insensitively', function (string $name): void {
    asSuperAdmin();

    CareTeamRole::factory()->create(['name' => 'Advisor', 'type' => CareTeamRoleType::Prospect]);
    $careTeamRole = CareTeamRole::factory()->create(['name' => 'Mentor', 'type' => CareTeamRoleType::Prospect]);

    Livewire::test(EditProspectCareTeamRole::class, ['record' => $careTeamRole->getRouteKey()])
        ->fillForm(['name' => $name])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);

    expect($careTeamRole->refresh()->name)->toBe('Mentor');
})->with([
    'exact match' => ['Advisor'],
    'case variant' => ['advisor'],
]);

it('allows renaming to a name that was freed up by a soft-deleted care team role', function (): void {
    asSuperAdmin();

    CareTeamRole::factory()->create(['name' => 'Advisor', 'type' => CareTeamRoleType::Prospect])->delete();
    $careTeamRole = CareTeamRole::factory()->create(['name' => 'Mentor', 'type' => CareTeamRoleType::Prospect]);

    Livewire::test(EditProspectCareTeamRole::class, ['record' => $careTeamRole->getRouteKey()])
        ->fillForm(['name' => 'advisor'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($careTeamRole->refresh()->name)->toBe('advisor');
});

it('allows a care team role to keep or change the case of its own name', function (string $name): void {
    asSuperAdmin();

    $careTeamRole = CareTeamRole::factory()->create(['name' => 'Advisor', 'type' => CareTeamRoleType::Prospect]);

    Livewire::test(EditProspectCareTeamRole::class, ['record' => $careTeamRole->getRouteKey()])
        ->fillForm(['name' => $name])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($careTeamRole->refresh()->name)->toBe($name);
})->with([
    'same name' => ['Advisor'],
    'case change' => ['ADVISOR'],
]);

it('allows renaming to a name used by a student care team role', function (): void {
    asSuperAdmin();

    CareTeamRole::factory()->create(['name' => 'Advisor', 'type' => CareTeamRoleType::Student]);
    $careTeamRole = CareTeamRole::factory()->create(['name' => 'Mentor', 'type' => CareTeamRoleType::Prospect]);

    Livewire::test(EditProspectCareTeamRole::class, ['record' => $careTeamRole->getRouteKey()])
        ->fillForm(['name' => 'advisor'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($careTeamRole->refresh()->name)->toBe('advisor');
});

// TODO: Cleanup Task (care-team-role-name-citext) - delete this test
it('rejects renaming to a soft-deleted name before the citext migration activates', function (): void {
    asSuperAdmin();

    CareTeamRoleNameCitextFeature::deactivate();

    CareTeamRole::factory()->create(['name' => 'Advisor', 'type' => CareTeamRoleType::Prospect])->delete();
    $careTeamRole = CareTeamRole::factory()->create(['name' => 'Mentor', 'type' => CareTeamRoleType::Prospect]);

    Livewire::test(EditProspectCareTeamRole::class, ['record' => $careTeamRole->getRouteKey()])
        ->fillForm(['name' => 'Advisor'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);
});
