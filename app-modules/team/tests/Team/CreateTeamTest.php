<?php

use App\Models\User;
use Assist\Team\Models\Team;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Pest\Laravel\assertDatabaseHas;

use Assist\Team\Filament\Resources\TeamResource;
use Assist\Team\Filament\Resources\TeamResource\Pages\CreateTeam;

// Permission Tests

test('CreateTeam is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            TeamResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateTeam::class)
        ->assertForbidden();

    $user->givePermissionTo('team.view-any');
    $user->givePermissionTo('team.create');

    actingAs($user)
        ->get(
            TeamResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(Team::factory()->make());

    livewire(CreateTeam::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, Team::all());

    assertDatabaseHas(Team::class, $request->toArray());
});
