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
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubQualityResource;
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubQualityResource\Pages\CreateResourceHubQuality;
use AdvisingApp\ResourceHub\Models\ResourceHubQuality;
use AdvisingApp\ResourceHub\Tests\Tenant\ResourceHubQuality\RequestFactories\CreateResourceHubQualityRequestFactory;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;

// TODO: Write CreateResourceHubQuality tests
//test('A successful action on the CreateResourceHubQuality page', function () {});
//
//test('CreateResourceHubQuality requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('CreateResourceHubQuality is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            ResourceHubQualityResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateResourceHubQuality::class)
        ->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.create');

    actingAs($user)
        ->get(
            ResourceHubQualityResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateResourceHubQualityRequestFactory::new()->create());

    livewire(CreateResourceHubQuality::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ResourceHubQuality::all());

    assertDatabaseHas(ResourceHubQuality::class, $request->toArray());
});

test('CreateResourceHubQuality is gated with proper feature ccess control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->resourceHub = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.create');

    actingAs($user)
        ->get(
            ResourceHubQualityResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateResourceHubQuality::class)
        ->assertForbidden();

    $settings->data->addons->resourceHub = true;

    $settings->save();

    actingAs($user)
        ->get(
            ResourceHubQualityResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateResourceHubQualityRequestFactory::new()->create());

    livewire(CreateResourceHubQuality::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ResourceHubQuality::all());

    assertDatabaseHas(ResourceHubQuality::class, $request->toArray());
});
