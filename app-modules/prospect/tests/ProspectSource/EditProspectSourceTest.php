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

use AdvisingApp\Prospect\Filament\Resources\ProspectSourceResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Tests\ProspectSource\RequestFactories\EditProspectSourceRequestFactory;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertEquals;
use function Tests\asSuperAdmin;

test('A successful action on the EditProspectSource page', function () {
    $prospectSource = ProspectSource::factory()->create();

    asSuperAdmin()
        ->get(
            ProspectSourceResource::getUrl('edit', [
                'record' => $prospectSource->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditProspectSourceRequestFactory::new()->create();

    livewire(ProspectSourceResource\Pages\EditProspectSource::class, [
        'record' => $prospectSource->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $prospectSource->name,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $prospectSource->fresh()->name);
});

test('EditProspectSource requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $prospectSource = ProspectSource::factory()->create();

    livewire(ProspectSourceResource\Pages\EditProspectSource::class, [
        'record' => $prospectSource->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $prospectSource->name,
        ])
        ->fillForm(EditProspectSourceRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ProspectSource::class, $prospectSource->toArray());
})->with(
    [
        'name missing' => [EditProspectSourceRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [EditProspectSourceRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
    ]
);

// Permission Tests

test('EditProspectSource is gated with proper access control', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $prospectSource = ProspectSource::factory()->create();

    actingAs($user)
        ->get(
            ProspectSourceResource::getUrl('edit', [
                'record' => $prospectSource,
            ])
        )->assertForbidden();

    livewire(ProspectSourceResource\Pages\EditProspectSource::class, [
        'record' => $prospectSource->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    actingAs($user)
        ->get(
            ProspectSourceResource::getUrl('edit', [
                'record' => $prospectSource,
            ])
        )->assertSuccessful();

    $request = collect(EditProspectSourceRequestFactory::new()->create());

    livewire(ProspectSourceResource\Pages\EditProspectSource::class, [
        'record' => $prospectSource->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $prospectSource->fresh()->name);
});
