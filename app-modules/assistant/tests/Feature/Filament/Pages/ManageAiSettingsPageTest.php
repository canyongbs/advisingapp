<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

use Assist\Assistant\Filament\Pages\ManageAiSettings;

it('renders successfully', function () {
    asSuperAdmin();

    Livewire::test(ManageAiSettings::class)
        ->assertStatus(200);
});

it('does not load if you do not have any permissions to access', function () {
    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(ManageAiSettings::class)
        ->assertStatus(403);
});

it('loads if you have the correct access to ai settings', function () {
    $user = User::factory()->create();

    $user->givePermissionTo(['assistant.access_ai_settings']);

    actingAs($user);

    Livewire::test(ManageAiSettings::class)
        ->assertStatus(200);
});
