<?php

use App\Models\User;
use Livewire\Livewire;

use function Tests\asSuperAdmin;

use App\Filament\Pages\Dashboard;

use function Pest\Laravel\{actingAs};

use Assist\Consent\Models\ConsentAgreement;
use Assist\Consent\Enums\ConsentAgreementType;
use Assist\Assistant\Filament\Pages\PersonalAssistant;

it('renders successfully', function () {
    ConsentAgreement::factory()->create([
        'type' => ConsentAgreementType::AzureOpenAI,
    ]);

    asSuperAdmin();

    Livewire::test(PersonalAssistant::class)
        ->assertStatus(200);
});

it('is properly gated with access control', function () {
    ConsentAgreement::factory()->create([
        'type' => ConsentAgreementType::AzureOpenAI,
    ]);

    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(PersonalAssistant::class)
        ->assertStatus(403);

    $user->givePermissionTo('assistant.access');

    Livewire::test(PersonalAssistant::class)
        ->assertStatus(200);
});

it('will show a consent modal if the user has not yet agreed to the terms and conditions of use', function () {
    $consentAgreement = ConsentAgreement::factory()->create([
        'type' => ConsentAgreementType::AzureOpenAI,
    ]);

    $user = User::factory()->create();
    $user->givePermissionTo('assistant.access');

    actingAs($user);

    Livewire::test(PersonalAssistant::class)
        ->call('determineIfConsentWasGiven')
        ->assertViewHas('consentedToTerms', false)
        ->assertSee($consentAgreement->title)
        ->assertSeeHtml(str($consentAgreement->description)->markdown()->sanitizeHtml()->toHtmlString())
        ->assertSeeHtml(str($consentAgreement->body)->markdown()->sanitizeHtml()->toHtmlString());
});

it('will show the AI Assistant interface if the user has agreed to the terms and conditions of use', function () {
    $consentAgreement = ConsentAgreement::factory()->create([
        'type' => ConsentAgreementType::AzureOpenAI,
    ]);

    $user = User::factory()->create();
    $user->givePermissionTo('assistant.access');

    $user->consentTo($consentAgreement);

    actingAs($user);

    Livewire::test(PersonalAssistant::class)
        ->call('determineIfConsentWasGiven')
        ->assertViewHas('consentedToTerms', true)
        ->assertDontSee($consentAgreement->title)
        ->assertDontSee($consentAgreement->description)
        ->assertDontSee($consentAgreement->body);
});

it('will redirect the user back to the dashboard if they dismiss the consent modal', function () {
    ConsentAgreement::factory()->create([
        'type' => ConsentAgreementType::AzureOpenAI,
    ]);

    $user = User::factory()->create();
    $user->givePermissionTo('assistant.access');

    actingAs($user);

    Livewire::test(PersonalAssistant::class)
        ->call('denyConsent')
        ->assertRedirect(Dashboard::getUrl());
});

it('will allow a user to access the AI Assistant interface if they agree to the terms and conditions of use', function () {
    $consentAgreement = ConsentAgreement::factory()->create([
        'type' => ConsentAgreementType::AzureOpenAI,
    ]);

    $user = User::factory()->create();
    $user->givePermissionTo('assistant.access');

    actingAs($user);

    expect($user->hasConsentedTo($consentAgreement))->toBeFalse();

    $aiAssistant = Livewire::test(PersonalAssistant::class);

    $aiAssistant
        ->call('determineIfConsentWasGiven')
        ->assertViewHas('consentedToTerms', false)
        ->assertSee($consentAgreement->title)
        ->assertSeeHtml(str($consentAgreement->description)->markdown()->sanitizeHtml()->toHtmlString())
        ->assertSeeHtml(str($consentAgreement->body)->markdown()->sanitizeHtml()->toHtmlString());

    $aiAssistant
        ->set('consentedToTerms', true)
        ->call('confirmConsent')
        ->assertDontSee($consentAgreement->title)
        ->assertDontSee($consentAgreement->description)
        ->assertDontSee($consentAgreement->body);

    expect($user->hasConsentedTo($consentAgreement))->toBeTrue();
});
