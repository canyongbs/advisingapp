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

use AdvisingApp\Ai\Enums\EmployeeAdvisorResourceHubArticleAccess;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\CustomerAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\ManageCustomerAdvisorResourceHub;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\IntegrationOpenAi\Jobs\UploadCustomerAdvisorFilesToVectorStore;
use AdvisingApp\ResourceHub\Models\ResourceHubCategory;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();
});

test('Manage Customer Advisor Resource Hub is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $customerAdvisor = CustomerAdvisor::factory()->create();

    actingAs($user)
        ->get(
            CustomerAdvisorResource::getUrl('manage-resource-hub', [
                'record' => $customerAdvisor,
            ])
        )->assertForbidden();

    livewire(ManageCustomerAdvisorResourceHub::class, [
        'record' => $customerAdvisor->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.*.update']);

    actingAs($user)
        ->get(
            CustomerAdvisorResource::getUrl('manage-resource-hub', [
                'record' => $customerAdvisor,
            ])
        )->assertSuccessful();
});

test('can enable resource hub knowledge with an article access level and categories', function () {
    Queue::fake();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.*.update']);

    $customerAdvisor = CustomerAdvisor::factory()->create([
        'has_resource_hub_knowledge' => false,
        'resource_hub_article_access' => null,
    ]);

    $categoryA = ResourceHubCategory::factory()->create();
    $categoryB = ResourceHubCategory::factory()->create();

    actingAs($user);

    livewire(ManageCustomerAdvisorResourceHub::class, [
        'record' => $customerAdvisor->getRouteKey(),
    ])
        ->fillForm([
            'has_resource_hub_knowledge' => true,
            'resource_hub_article_access' => EmployeeAdvisorResourceHubArticleAccess::Public->value,
            'resource_hub_categories' => [$categoryA->getKey(), $categoryB->getKey()],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(CustomerAdvisor::class, [
        'id' => $customerAdvisor->getKey(),
        'has_resource_hub_knowledge' => true,
        'resource_hub_article_access' => EmployeeAdvisorResourceHubArticleAccess::Public->value,
    ]);

    assertDatabaseHas('customer_advisor_resource_hub_categories', [
        'customer_advisor_id' => $customerAdvisor->getKey(),
        'resource_hub_category_id' => $categoryA->getKey(),
    ]);

    assertDatabaseHas('customer_advisor_resource_hub_categories', [
        'customer_advisor_id' => $customerAdvisor->getKey(),
        'resource_hub_category_id' => $categoryB->getKey(),
    ]);

    Queue::assertPushed(UploadCustomerAdvisorFilesToVectorStore::class);
});

test('attaching a resource hub category dispatches the vector store upload job', function () {
    Queue::fake();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.*.update']);

    $customerAdvisor = CustomerAdvisor::factory()->create([
        'has_resource_hub_knowledge' => true,
        'resource_hub_article_access' => EmployeeAdvisorResourceHubArticleAccess::Public,
    ]);

    $category = ResourceHubCategory::factory()->create();

    actingAs($user);

    // Reset the fake so only the dispatch caused by attaching the category below is recorded,
    // isolating it from the dispatch already fired by creating the advisor above.
    Queue::fake();

    livewire(ManageCustomerAdvisorResourceHub::class, [
        'record' => $customerAdvisor->getRouteKey(),
    ])
        ->fillForm([
            'has_resource_hub_knowledge' => true,
            'resource_hub_article_access' => EmployeeAdvisorResourceHubArticleAccess::Public->value,
            'resource_hub_categories' => [$category->getKey()],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas('customer_advisor_resource_hub_categories', [
        'customer_advisor_id' => $customerAdvisor->getKey(),
        'resource_hub_category_id' => $category->getKey(),
    ]);

    Queue::assertPushed(UploadCustomerAdvisorFilesToVectorStore::class);
});

test('detaching a resource hub category dispatches the vector store upload job', function () {
    Queue::fake();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.*.update']);

    $customerAdvisor = CustomerAdvisor::factory()->create([
        'has_resource_hub_knowledge' => true,
        'resource_hub_article_access' => EmployeeAdvisorResourceHubArticleAccess::Public,
    ]);

    $category = ResourceHubCategory::factory()->create();
    $customerAdvisor->resourceHubCategories()->attach($category);

    actingAs($user);

    // Reset the fake so only the dispatch caused by detaching the category below is recorded,
    // isolating it from the dispatches already fired by creating the advisor and attaching the
    // category above.
    Queue::fake();

    livewire(ManageCustomerAdvisorResourceHub::class, [
        'record' => $customerAdvisor->getRouteKey(),
    ])
        ->fillForm([
            'has_resource_hub_knowledge' => true,
            'resource_hub_article_access' => EmployeeAdvisorResourceHubArticleAccess::Public->value,
            'resource_hub_categories' => [],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseMissing('customer_advisor_resource_hub_categories', [
        'customer_advisor_id' => $customerAdvisor->getKey(),
        'resource_hub_category_id' => $category->getKey(),
    ]);

    Queue::assertPushed(UploadCustomerAdvisorFilesToVectorStore::class);
});

test('disabling resource hub knowledge clears the article access level', function () {
    Queue::fake();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.*.update']);

    $customerAdvisor = CustomerAdvisor::factory()->create([
        'has_resource_hub_knowledge' => true,
        'resource_hub_article_access' => EmployeeAdvisorResourceHubArticleAccess::All,
    ]);

    actingAs($user);

    livewire(ManageCustomerAdvisorResourceHub::class, [
        'record' => $customerAdvisor->getRouteKey(),
    ])
        ->fillForm([
            'has_resource_hub_knowledge' => false,
            'resource_hub_article_access' => EmployeeAdvisorResourceHubArticleAccess::All->value,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(CustomerAdvisor::class, [
        'id' => $customerAdvisor->getKey(),
        'has_resource_hub_knowledge' => false,
        'resource_hub_article_access' => null,
    ]);
});

test('the article access and categories fields are hidden until resource hub knowledge is enabled', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.*.update']);

    $customerAdvisor = CustomerAdvisor::factory()->create([
        'has_resource_hub_knowledge' => false,
    ]);

    actingAs($user);

    livewire(ManageCustomerAdvisorResourceHub::class, [
        'record' => $customerAdvisor->getRouteKey(),
    ])
        ->assertFormFieldIsHidden('resource_hub_article_access')
        ->assertFormFieldIsHidden('resource_hub_categories')
        ->set('data.has_resource_hub_knowledge', true)
        ->assertFormFieldIsVisible('resource_hub_article_access')
        ->assertFormFieldIsVisible('resource_hub_categories');
});
