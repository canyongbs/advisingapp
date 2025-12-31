<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Interaction\Filament\Resources\InteractionInitiatives\InteractionInitiativeResource;
use AdvisingApp\Interaction\Filament\Resources\InteractionInitiatives\Pages\EditInteractionInitiative;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('EditInteractionInitative is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $initiative = InteractionInitiative::factory()->create();

    actingAs($user)
        ->get(
            InteractionInitiativeResource::getUrl('edit', ['record' => $initiative])
        )->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.update');

    actingAs($user)
        ->get(
            InteractionInitiativeResource::getUrl('edit', ['record' => $initiative])
        )->assertSuccessful();
});

test('it cannot delete instances used by an interaction', function () {
    asSuperAdmin();

    $initiative = InteractionInitiative::factory()->create();

    Interaction::factory()->for($initiative, 'initiative')->create();

    livewire(EditInteractionInitiative::class, ['record' => $initiative->id])
        ->assertActionHidden('delete');
});