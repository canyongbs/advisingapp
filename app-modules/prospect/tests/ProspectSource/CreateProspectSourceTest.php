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

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

use Assist\Prospect\Filament\Resources\ProspectSourceResource;
use Assist\Prospect\Tests\ProspectSource\RequestFactories\CreateProspectSourceRequestFactory;

test('A successful action on the CreateProspectSource page', function () {
    asSuperAdmin()
        ->get(
            ProspectSourceResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = CreateProspectSourceRequestFactory::new()->create();

    livewire(ProspectSourceResource\Pages\CreateProspectSource::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ProspectSource::all());

    assertDatabaseHas(ProspectSource::class, $request);
});

test('CreateProspectSource requires valid data', function ($data, $errors) {
    asSuperAdmin();

    livewire(ProspectSourceResource\Pages\CreateProspectSource::class)
        ->fillForm(CreateProspectSourceRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(ProspectSource::all());
})->with(
    [
        'name missing' => [CreateProspectSourceRequestFactory::new()->without('name'), ['name' => 'required']],
        'name not a string' => [CreateProspectSourceRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
    ]
);

// Permission Tests

test('CreateProspectSource is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ProspectSourceResource::getUrl('create')
        )->assertForbidden();

    livewire(ProspectSourceResource\Pages\CreateProspectSource::class)
        ->assertForbidden();

    $user->givePermissionTo('prospect_source.view-any');
    $user->givePermissionTo('prospect_source.create');

    actingAs($user)
        ->get(
            ProspectSourceResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateProspectSourceRequestFactory::new()->create());

    livewire(ProspectSourceResource\Pages\CreateProspectSource::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ProspectSource::all());

    assertDatabaseHas(ProspectSource::class, $request->toArray());
});
