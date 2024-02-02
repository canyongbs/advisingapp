<?php

/*
<COPYRIGHT>

    Copyright © 2022-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;
use Livewire\Livewire;

use function Tests\asSuperAdmin;

use App\Filament\Pages\Dashboard;

use function Pest\Laravel\{actingAs};

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Consent\Models\ConsentAgreement;
use AdvisingApp\Consent\Enums\ConsentAgreementType;
use AdvisingApp\Assistant\Filament\Pages\PersonalAssistant;

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

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

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

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
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

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
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

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
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

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
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
