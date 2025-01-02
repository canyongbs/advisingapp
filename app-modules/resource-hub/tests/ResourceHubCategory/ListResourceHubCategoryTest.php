<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubCategoryResource;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;

// TODO: Write ListResourceHubCategory tests
//test('The correct details are displayed on the ListResourceHubCategory page', function () {});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListResourceHubCategory is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            ResourceHubCategoryResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');

    actingAs($user)
        ->get(
            ResourceHubCategoryResource::getUrl('index')
        )->assertSuccessful();
});

test('ListResourceHubCategory is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->resourceHub = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('product_admin.view-any');

    actingAs($user)
        ->get(
            ResourceHubCategoryResource::getUrl('index')
        )->assertForbidden();

    $settings->data->addons->resourceHub = true;

    $settings->save();

    actingAs($user)
        ->get(
            ResourceHubCategoryResource::getUrl('index')
        )->assertSuccessful();
});

test('ListResourceHubCategory is gated with proper license access control', function () {
    $settings = app(LicenseSettings::class);

    // When the feature is enabled
    $settings->data->addons->resourceHub = true;

    $settings->save();

    $user = User::factory()->create();

    // And the authenticatable has the correct permissions
    // But they do not have the appropriate license
    $user->givePermissionTo('product_admin.view-any');

    // They should not be able to access the resource
    actingAs($user)
        ->get(
            ResourceHubCategoryResource::getUrl('index')
        )->assertForbidden();

    $user->grantLicense(LicenseType::RecruitmentCrm);

    $user->refresh();

    actingAs($user)
        ->get(
            ResourceHubCategoryResource::getUrl('index')
        )->assertSuccessful();
});
