<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\CustomerAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\ListCustomerAdvisors;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Features\RenameQnaAdvisorsFeature;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

test('List Customer Advisors is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    actingAs($user)
        ->get(
            CustomerAdvisorResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo(RenameQnaAdvisorsFeature::active() ? 'customer_advisor.view-any' : 'qna_advisor.view-any');

    actingAs($user)
        ->get(
            CustomerAdvisorResource::getUrl('index')
        )->assertSuccessful();
});

it('render Customer Advisors default to without archived', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo(RenameQnaAdvisorsFeature::active() ? ['customer_advisor.view-any', 'customer_advisor.*.view'] : ['qna_advisor.view-any', 'qna_advisor.*.view']);

    actingAs($user);

    $customerAdvisors = CustomerAdvisor::factory()->count(3)->state([
        'archived_at' => null,
    ])->create();

    $archivedCustomerAdvisors = CustomerAdvisor::factory()->count(3)->state([
        'archived_at' => now(),
    ])->create();

    livewire(ListCustomerAdvisors::class)
        ->assertCanSeeTableRecords($customerAdvisors)
        ->assertCanNotSeeTableRecords($archivedCustomerAdvisors);
});

it('filter Customer Advisors with archived', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo(RenameQnaAdvisorsFeature::active() ? ['customer_advisor.view-any', 'customer_advisor.*.view'] : ['qna_advisor.view-any', 'qna_advisor.*.view']);

    actingAs($user);

    $customerAdvisors = CustomerAdvisor::factory()->count(2)->state([
        'archived_at' => null,
    ])->create();

    $archivedCustomerAdvisors = CustomerAdvisor::factory()->count(2)->state([
        'archived_at' => now(),
    ])->create();

    livewire(ListCustomerAdvisors::class)
        ->assertCanSeeTableRecords($customerAdvisors)
        ->assertCanNotSeeTableRecords($archivedCustomerAdvisors)
        ->removeTableFilter('withoutArchived')
        ->assertCanSeeTableRecords($customerAdvisors->merge($archivedCustomerAdvisors));
});
