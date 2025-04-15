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

use AdvisingApp\CareTeam\Filament\Resources\ProspectCareTeamRoleResource;
use AdvisingApp\CareTeam\Filament\Resources\ProspectCareTeamRoleResource\Pages\EditProspectCareTeamRole;
use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\CareTeam\Tests\Tenant\RequestFactories\EditCareTeamRoleRequestFactory;
use AdvisingApp\Prospect\Models\Prospect;
use App\Enums\CareTeamRoleType;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertEquals;
use function Tests\asSuperAdmin;


test('EditProspectCareTeamRole is gated with proper access control', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $careTeamRole = CareTeamRole::factory()->create(['type' => CareTeamRoleType::Prospect]);

    actingAs($user)
        ->get(
            ProspectCareTeamRoleResource::getUrl('edit', [
                'record' => $careTeamRole,
            ])
        )->assertForbidden();

    livewire(EditProspectCareTeamRole::class, [
        'record' => $careTeamRole->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    actingAs($user)
        ->get(
            ProspectCareTeamRoleResource::getUrl('edit', [
                'record' => $careTeamRole,
            ])
        )->assertSuccessful();

        livewire(EditProspectCareTeamRole::class, [
            'record' => $careTeamRole->getRouteKey(),
        ])
            ->assertSuccessful();
});

test('A successful action on the EditProspectCareTeamRole page', function () {
    $careTeamRole = CareTeamRole::factory()->create(['type' => CareTeamRoleType::Prospect]);

    asSuperAdmin()
        ->get(
            ProspectCareTeamRoleResource::getUrl('edit', [
                'record' => $careTeamRole->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditCareTeamRoleRequestFactory::new()->state(['type' => CareTeamRoleType::Prospect])->create();

    livewire(EditProspectCareTeamRole::class, [
        'record' => $careTeamRole->getRouteKey(),
    ])
        ->set('data', $editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $careTeamRole->fresh()->name);
});

test('EditProspectCareTeamRole requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $careTeamRole = CareTeamRole::factory()->create(['type' => CareTeamRoleType::Prospect]);

    $editRequest = EditCareTeamRoleRequestFactory::new($data)->create();

    livewire(EditProspectCareTeamRole::class, [
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

