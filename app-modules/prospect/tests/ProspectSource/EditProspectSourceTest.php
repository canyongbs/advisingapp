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

use Assist\Prospect\Models\ProspectSource;

use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

use Assist\Prospect\Filament\Resources\ProspectSourceResource;
use Assist\Prospect\Tests\ProspectSource\RequestFactories\EditProspectSourceRequestFactory;

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
    $user = User::factory()->create();

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

    $user->givePermissionTo('prospect_source.view-any');
    $user->givePermissionTo('prospect_source.*.update');

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
