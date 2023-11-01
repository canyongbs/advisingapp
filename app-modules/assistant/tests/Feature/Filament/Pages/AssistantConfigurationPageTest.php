<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Assistant\Filament\Pages\ManageAiSettings;
use Assist\Assistant\Filament\Pages\AssistantConfiguration;
use Assist\Consent\Filament\Resources\ConsentAgreementResource\Pages\ListConsentAgreements;

it('does not load if you do not have any permissions to access', function () {
    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(AssistantConfiguration::class)
        ->assertStatus(403);
});

it('redirects if you have the correct access to consent agreements', function () {
    $user = User::factory()->create();

    $user->givePermissionTo(['consent_agreement.view-any', 'consent_agreement.*.view', 'consent_agreement.*.update']);

    actingAs($user);

    Livewire::test(AssistantConfiguration::class)
        ->assertRedirect(ListConsentAgreements::getUrl());
});

it('redirects if you have the correct access to ai settings', function () {
    $user = User::factory()->create();

    $user->givePermissionTo(['assistant.access_ai_settings']);

    actingAs($user);

    Livewire::test(AssistantConfiguration::class)
        ->assertRedirect(ManageAiSettings::getUrl());
});

it('redirects if you have access for both ai settings and consent agreements', function () {
    $user = User::factory()->create();

    $user->givePermissionTo(['consent_agreement.view-any', 'consent_agreement.*.view', 'consent_agreement.*.update', 'assistant.access_ai_settings']);

    actingAs($user);

    Livewire::test(AssistantConfiguration::class)
        ->assertRedirect(ListConsentAgreements::getUrl());
});
