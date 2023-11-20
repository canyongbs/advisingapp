<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Prospect\Models\Prospect;

use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Pest\Laravel\assertDatabaseHas;

use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\Prospect\Tests\Prospect\RequestFactories\CreateProspectRequestFactory;

// TODO: Write CreateProspect page tests
//test('A successful action on the CreateProspect page', function () {});
//
//test('CreateProspect requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('CreateProspect is gated with proper access control', function () {
    $user = User::factory()->create();

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

    $request = collect(CreateProspectRequestFactory::new()->create());

    livewire(ProspectResource\Pages\CreateProspect::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, Prospect::all());

    assertDatabaseHas(Prospect::class, $request->toArray());
});
