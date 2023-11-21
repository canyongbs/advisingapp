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

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

use Assist\Prospect\Filament\Resources\ProspectStatusResource;
use Assist\Prospect\Tests\ProspectStatus\RequestFactories\CreateProspectStatusRequestFactory;

test('A successful action on the CreateProspectStatus page', function () {
    asSuperAdmin()
        ->get(
            ProspectStatusResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = CreateProspectStatusRequestFactory::new()->create();

    livewire(ProspectStatusResource\Pages\CreateProspectStatus::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ProspectStatus::all());

    assertDatabaseHas(ProspectStatus::class, $request);
});

test('CreateProspectStatus requires valid data', function ($data, $errors) {
    asSuperAdmin();

    livewire(ProspectStatusResource\Pages\CreateProspectStatus::class)
        ->fillForm(CreateProspectStatusRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(ProspectStatus::all());
})->with(
    [
        'name missing' => [CreateProspectStatusRequestFactory::new()->without('name'), ['name' => 'required']],
        'name not a string' => [CreateProspectStatusRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'color missing' => [CreateProspectStatusRequestFactory::new()->state(['color' => null]), ['color' => 'required']],
        'color not within enum' => [CreateProspectStatusRequestFactory::new()->state(['color' => 'not-a-color']), ['color' => Enum::class]],
    ]
);

// Permission Tests

test('CreateProspectStatus is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ProspectStatusResource::getUrl('create')
        )->assertForbidden();

    livewire(ProspectStatusResource\Pages\CreateProspectStatus::class)
        ->assertForbidden();

    $user->givePermissionTo('prospect_status.view-any');
    $user->givePermissionTo('prospect_status.create');

    actingAs($user)
        ->get(
            ProspectStatusResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateProspectStatusRequestFactory::new()->create());

    livewire(ProspectStatusResource\Pages\CreateProspectStatus::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ProspectStatus::all());

    assertDatabaseHas(ProspectStatus::class, $request->toArray());
});
