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

use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Tests\Tenant\Prospect\RequestFactories\CreateProspectRequestFactory;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;

// TODO: Write CreateProspect page tests
//test('A successful action on the CreateProspect page', function () {});
//
//test('CreateProspect requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('CreateProspect is gated with proper access control', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    actingAs($user)
        ->get(
            ProspectResource::getUrl('create')
        )->assertForbidden();

    livewire(ProspectResource\Pages\CreateProspect::class)
        ->assertForbidden();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.create');

    actingAs($user)
        ->get(
            ProspectResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateProspectRequestFactory::new()->create([
        'created_by_id' => $user->id,
    ]));

    livewire(ProspectResource\Pages\CreateProspect::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, Prospect::all());

    assertDatabaseHas(Prospect::class, $request->toArray());
});
