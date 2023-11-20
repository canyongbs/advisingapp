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
