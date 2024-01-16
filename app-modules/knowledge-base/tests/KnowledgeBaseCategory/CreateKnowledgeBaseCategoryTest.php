<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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
use AdvisingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseCategoryResource;

use AdvisingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AdvisingApp\KnowledgeBase\Tests\KnowledgeBaseCategory\RequestFactories\CreateKnowledgeBaseCategoryRequestFactory;
use App\Models\User;
use App\Settings\LicenseSettings;

use function PHPUnit\Framework\assertCount;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

// TODO: Write CreateKnowledgeBaseCategory tests
//test('A successful action on the CreateKnowledgeBaseCategory page', function () {});
//
//test('CreateKnowledgeBaseCategory requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('CreateKnowledgeBaseCategory is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            KnowledgeBaseCategoryResource::getUrl('create')
        )->assertForbidden();

    livewire(KnowledgeBaseCategoryResource\Pages\CreateKnowledgeBaseCategory::class)
        ->assertForbidden();

    $user->givePermissionTo('knowledge_base_category.view-any');
    $user->givePermissionTo('knowledge_base_category.create');

    actingAs($user)
        ->get(
            KnowledgeBaseCategoryResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateKnowledgeBaseCategoryRequestFactory::new()->create());

    livewire(KnowledgeBaseCategoryResource\Pages\CreateKnowledgeBaseCategory::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, KnowledgeBaseCategory::all());

    assertDatabaseHas(KnowledgeBaseCategory::class, $request->toArray());
});

test('CreateKnowledgeBaseCategory is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->knowledgeManagement = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('knowledge_base_category.view-any');
    $user->givePermissionTo('knowledge_base_category.create');

    actingAs($user)
        ->get(
            KnowledgeBaseCategoryResource::getUrl('create')
        )->assertForbidden();

    livewire(KnowledgeBaseCategoryResource\Pages\CreateKnowledgeBaseCategory::class)
        ->assertForbidden();

    $settings->data->addons->knowledgeManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            KnowledgeBaseCategoryResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateKnowledgeBaseCategoryRequestFactory::new()->create());

    livewire(KnowledgeBaseCategoryResource\Pages\CreateKnowledgeBaseCategory::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, KnowledgeBaseCategory::all());

    assertDatabaseHas(KnowledgeBaseCategory::class, $request->toArray());
});
