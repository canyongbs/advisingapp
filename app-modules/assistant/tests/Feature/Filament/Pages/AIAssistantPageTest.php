<?php

use App\Models\User;
use Livewire\Livewire;

use function Tests\asSuperAdmin;

use App\Filament\Pages\Dashboard;

use function Pest\Laravel\{actingAs};

use Assist\Consent\Models\ConsentAgreement;
use Assist\Consent\Enums\ConsentAgreementType;
use Assist\Assistant\Filament\Pages\AIAssistant;

it('renders successfully', function () {
    ConsentAgreement::factory()->create([
        'type' => ConsentAgreementType::AZURE_OPEN_AI,
    ]);

    asSuperAdmin();

    Livewire::test(AIAssistant::class)
        ->assertStatus(200);
});

it('is properly gated with access control', function () {
    ConsentAgreement::factory()->create([
        'type' => ConsentAgreementType::AZURE_OPEN_AI,
    ]);

    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(AIAssistant::class)
        ->assertStatus(403);

    $user->givePermissionTo('assistant.access');

    Livewire::test(AIAssistant::class)
        ->assertStatus(200);
});

it('will show a consent modal if the user has not yet agreed to the terms and conditions of use', function () {
    // Given that a user tries to access the AI Assistant page
    $consentAgreement = ConsentAgreement::factory()->create([
        'type' => ConsentAgreementType::AZURE_OPEN_AI,
    ]);

    $user = User::factory()->create();
    $user->givePermissionTo('assistant.access');

    actingAs($user);

    Livewire::test(AIAssistant::class)
        ->call('determineIfConsentWasGiven')
        ->assertViewHas('consentedToTerms', false)
        ->assertSee($consentAgreement->title)
        ->assertSee($consentAgreement->description)
        ->assertSee($consentAgreement->body);
});

it('will show the AI Assistant interface if the user has agreed to the terms and conditions of use', function () {
    $consentAgreement = ConsentAgreement::factory()->create([
        'type' => ConsentAgreementType::AZURE_OPEN_AI,
    ]);

    $user = User::factory()->create();
    $user->givePermissionTo('assistant.access');

    $user->consentTo($consentAgreement);

    actingAs($user);

    Livewire::test(AIAssistant::class)
        ->call('determineIfConsentWasGiven')
        ->assertViewHas('consentedToTerms', true)
        ->assertDontSee($consentAgreement->title)
        ->assertDontSee($consentAgreement->description)
        ->assertDontSee($consentAgreement->body);
});

it('will redirect the user back to the dashboard if they dismiss the consent modal', function () {
    ConsentAgreement::factory()->create([
        'type' => ConsentAgreementType::AZURE_OPEN_AI,
    ]);

    asSuperAdmin();

    Livewire::test(AIAssistant::class)
        ->call('denyConsent')
        ->assertRedirect(Dashboard::getUrl());
});

it('will allow a user to access the AI Assistant interface if they agree to the terms and conditions of use', function () {});
