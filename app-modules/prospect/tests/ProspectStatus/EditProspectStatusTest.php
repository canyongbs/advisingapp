<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Illuminate\Validation\Rules\Enum;
use Assist\Prospect\Models\ProspectStatus;

use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

use Assist\Prospect\Filament\Resources\ProspectStatusResource;
use Assist\Prospect\Tests\ProspectStatus\RequestFactories\EditProspectStatusRequestFactory;

test('A successful action on the EditProspectStatus page', function () {
    $prospectStatus = ProspectStatus::factory()->create();

    asSuperAdmin()
        ->get(
            ProspectStatusResource::getUrl('edit', [
                'record' => $prospectStatus->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditProspectStatusRequestFactory::new()->create();

    livewire(ProspectStatusResource\Pages\EditProspectStatus::class, [
        'record' => $prospectStatus->getRouteKey(),
    ])
        ->assertFormSet([
            'classification' => $prospectStatus->classification->value,
            'name' => $prospectStatus->name,
            'color' => $prospectStatus->color->value,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $prospectStatus->fresh()->name);
    assertEquals($editRequest['classification'], $prospectStatus->fresh()->classification);
    assertEquals($editRequest['color'], $prospectStatus->fresh()->color);
});

test('EditProspectStatus requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $prospectStatus = ProspectStatus::factory()->create();

    livewire(ProspectStatusResource\Pages\EditProspectStatus::class, [
        'record' => $prospectStatus->getRouteKey(),
    ])
        ->assertFormSet([
            'classification' => $prospectStatus->classification->value,
            'name' => $prospectStatus->name,
            'color' => $prospectStatus->color->value,
        ])
        ->fillForm(EditProspectStatusRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ProspectStatus::class, $prospectStatus->toArray());
})->with(
    [
        'name missing' => [EditProspectStatusRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [EditProspectStatusRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'color missing' => [EditProspectStatusRequestFactory::new()->state(['color' => null]), ['color' => 'required']],
        'color not within enum' => [EditProspectStatusRequestFactory::new()->state(['color' => 'not-a-color']), ['color' => Enum::class]],
    ]
);

// Permission Tests

test('EditProspectStatus is gated with proper access control', function () {
    $user = User::factory()->create();

    $prospectStatus = ProspectStatus::factory()->create();

    actingAs($user)
        ->get(
            ProspectStatusResource::getUrl('edit', [
                'record' => $prospectStatus,
            ])
        )->assertForbidden();

    livewire(ProspectStatusResource\Pages\EditProspectStatus::class, [
        'record' => $prospectStatus->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('prospect_status.view-any');
    $user->givePermissionTo('prospect_status.*.update');

    actingAs($user)
        ->get(
            ProspectStatusResource::getUrl('edit', [
                'record' => $prospectStatus,
            ])
        )->assertSuccessful();

    $request = collect(EditProspectStatusRequestFactory::new()->create());

    livewire(ProspectStatusResource\Pages\EditProspectStatus::class, [
        'record' => $prospectStatus->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $prospectStatus->fresh()->name);
    assertEquals($request['color'], $prospectStatus->fresh()->color);
});
