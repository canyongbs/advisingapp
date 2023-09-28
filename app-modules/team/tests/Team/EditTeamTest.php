<?php

use App\Models\User;
use Assist\Team\Models\Team;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Assist\Team\Filament\Resources\TeamResource;
use Assist\Team\Filament\Resources\TeamResource\Pages\EditTeam;

// Permission Tests

test('EditTeam is gated with proper access control', function () {
    $user = User::factory()->create();

    $team = Team::factory()->create();

    actingAs($user)
        ->get(
            TeamResource::getUrl('edit', [
                'record' => $team,
            ])
        )->assertForbidden();

    livewire(EditTeam::class, [
        'record' => $team->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('team.view-any');
    $user->givePermissionTo('team.*.update');

    actingAs($user)
        ->get(
            TeamResource::getUrl('edit', [
                'record' => $team,
            ])
        )->assertSuccessful();

    // TODO: Finish these tests to ensure changes are allowed
    /** @var Team $request */
    $request = Team::factory()->make();

    livewire(EditTeam::class, [
        'record' => $team->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    $team->refresh();

    expect($team->name)->toEqual($request->name)
        ->and($team->description)->toEqual($request->description);
});
