<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
