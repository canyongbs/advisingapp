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
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Illuminate\Validation\Rules\Enum;
use AdvisingApp\Prospect\Models\Prospect;

use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

use AdvisingApp\Prospect\Models\ProspectStatus;
use AdvisingApp\Prospect\Filament\Resources\ProspectStatusResource;
use AdvisingApp\Prospect\Filament\Resources\ProspectStatusResource\Pages\EditProspectStatus;
use AdvisingApp\Prospect\Tests\ProspectStatus\RequestFactories\EditProspectStatusRequestFactory;

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

    livewire(EditProspectStatus::class, [
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

    livewire(EditProspectStatus::class, [
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
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $prospectStatus = ProspectStatus::factory()->create();

    actingAs($user)
        ->get(
            ProspectStatusResource::getUrl('edit', [
                'record' => $prospectStatus,
            ])
        )->assertForbidden();

    livewire(EditProspectStatus::class, [
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

    livewire(EditProspectStatus::class, [
        'record' => $prospectStatus->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $prospectStatus->fresh()->name);
    assertEquals($request['color'], $prospectStatus->fresh()->color);
});

test('EditProspectStatus is gated with proper system protection access control', function () {
    $prospectStatus = ProspectStatus::factory()->create(['is_system_protected' => true]);

    asSuperAdmin()
        ->get(
            ProspectStatusResource::getUrl('edit', [
                'record' => $prospectStatus,
            ])
        )->assertForbidden();

    livewire(EditProspectStatus::class, [
        'record' => $prospectStatus->getRouteKey(),
    ])
        ->assertForbidden();

    $prospectStatus = ProspectStatus::factory()->create();

    asSuperAdmin()
        ->get(
            ProspectStatusResource::getUrl('edit', [
                'record' => $prospectStatus,
            ])
        )->assertSuccessful();

    $request = collect(EditProspectStatusRequestFactory::new()->create());

    livewire(EditProspectStatus::class, [
        'record' => $prospectStatus->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $prospectStatus->fresh()->name);
    assertEquals($request['color'], $prospectStatus->fresh()->color);
});
