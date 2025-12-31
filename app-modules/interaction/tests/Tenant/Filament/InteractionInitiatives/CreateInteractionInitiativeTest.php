<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Interaction\Enums\InteractableType;
use AdvisingApp\Interaction\Filament\Resources\InteractionInitiatives\InteractionInitiativeResource;
use AdvisingApp\Interaction\Filament\Resources\InteractionInitiatives\Pages\CreateInteractionInitiative;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('CreateInteractionInitiative is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            InteractionInitiativeResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.create');

    actingAs($user)
        ->get(
            InteractionInitiativeResource::getUrl('create')
        )->assertSuccessful();
});

test('it can successfully create for student or for prospect', function () {
    asSuperAdmin();

    $studentInteractionInitiative = InteractionInitiative::factory()->make(['interactable_type' => InteractableType::Student]);
    $prospectInteractionInitiative = InteractionInitiative::factory()->make(['interactable_type' => InteractableType::Prospect]);

    assertDatabaseCount(InteractionInitiative::class, 0);

    livewire(CreateInteractionInitiative::class)
        ->assertSuccessful()
        ->fillForm($studentInteractionInitiative->toArray())
        ->call('create')
        ->assertHasNoFormErrors();
    livewire(CreateInteractionInitiative::class)
        ->assertSuccessful()
        ->fillForm($prospectInteractionInitiative->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseCount(InteractionInitiative::class, 2);

    assertDatabaseHas(InteractionInitiative::class, $studentInteractionInitiative->toArray());
    assertDatabaseHas(InteractionInitiative::class, $prospectInteractionInitiative->toArray());
});

test('it can only create if the name is unique per type', function () {
    asSuperAdmin();

    $interactionInitiative1 = InteractionInitiative::factory()->make(['name' => 'test', 'interactable_type' => InteractableType::Student]);
    $interactionInitiative2 = InteractionInitiative::factory()->make(['name' => 'test', 'interactable_type' => InteractableType::Student]);

    assertDatabaseCount(InteractionInitiative::class, 0);

    livewire(CreateInteractionInitiative::class)
        ->fillForm($interactionInitiative1->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    livewire(CreateInteractionInitiative::class)
        ->fillForm($interactionInitiative2->toArray())
        ->call('create')
        ->assertHasFormErrors();

    assertDatabaseCount(InteractionInitiative::class, 1);
});

test('it can successfully create with the same name for different types', function () {
    asSuperAdmin();

    $studentInteractionInitiative = InteractionInitiative::factory()->make(['name' => 'test', 'interactable_type' => InteractableType::Student]);
    $prospectInteractionInitiative = InteractionInitiative::factory()->make(['name' => 'test', 'interactable_type' => InteractableType::Prospect]);

    assertDatabaseCount(InteractionInitiative::class, 0);

    livewire(CreateInteractionInitiative::class)
        ->assertSuccessful()
        ->fillForm($studentInteractionInitiative->toArray())
        ->call('create')
        ->assertHasNoFormErrors();
    livewire(CreateInteractionInitiative::class)
        ->assertSuccessful()
        ->fillForm($prospectInteractionInitiative->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseCount(InteractionInitiative::class, 2);

    assertDatabaseHas(InteractionInitiative::class, $studentInteractionInitiative->toArray());
    assertDatabaseHas(InteractionInitiative::class, $prospectInteractionInitiative->toArray());
});

test('it can successfully set a default per type', function () {
    asSuperAdmin();

    $studentInteractionInitiative = InteractionInitiative::factory()->make(['is_default' => true, 'interactable_type' => InteractableType::Student]);
    $prospectInteractionInitiative = InteractionInitiative::factory()->make(['is_default' => true, 'interactable_type' => InteractableType::Prospect]);

    assertDatabaseCount(InteractionInitiative::class, 0);

    livewire(CreateInteractionInitiative::class)
        ->assertSuccessful()
        ->fillForm($studentInteractionInitiative->toArray())
        ->call('create')
        ->assertHasNoFormErrors();
    livewire(CreateInteractionInitiative::class)
        ->assertSuccessful()
        ->fillForm($prospectInteractionInitiative->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseCount(InteractionInitiative::class, 2);

    assertDatabaseHas(InteractionInitiative::class, $studentInteractionInitiative->toArray());
    assertDatabaseHas(InteractionInitiative::class, $prospectInteractionInitiative->toArray());
});
