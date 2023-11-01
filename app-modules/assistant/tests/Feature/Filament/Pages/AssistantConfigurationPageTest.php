<?php

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;

use Assist\Assistant\Filament\Pages\AssistantConfiguration;

it('renders successfully', function () {
    asSuperAdmin();

    Livewire::test(AssistantConfiguration::class)
        ->assertStatus(200);
});

it('does not load if you do not have any permissions to access', function () {
    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(AssistantConfiguration::class)
        ->assertStatus(403);
});

it('loads if you have the correct access to consent agreements', function () {
    $user = User::factory()->create();

    $user->givePermissionTo(['consent_agreement.view-any', 'consent_agreement.*.view', 'consent_agreement.*.update']);

    actingAs($user);

    Livewire::test(AssistantConfiguration::class)
        ->assertStatus(200)
        ->assertSee('User Agreement')
        ->assertDontSee('Manage AI Settings');
});

it('loads if you have the correct access to ai settings', function () {
    $user = User::factory()->create();

    $user->givePermissionTo(['assistant.access_ai_settings']);

    actingAs($user);

    Livewire::test(AssistantConfiguration::class)
        ->assertStatus(200)
        ->assertDontSee('User Agreement')
        ->assertSee('Manage AI Settings');
});

it('loads if you have access for both ai settings and consent agreements', function () {
    $user = User::factory()->create();

    $user->givePermissionTo(['consent_agreement.view-any', 'consent_agreement.*.view', 'consent_agreement.*.update', 'assistant.access_ai_settings']);

    actingAs($user);

    Livewire::test(AssistantConfiguration::class)
        ->assertStatus(200)
        ->assertSee('User Agreement')
        ->assertSee('Manage AI Settings');
});
