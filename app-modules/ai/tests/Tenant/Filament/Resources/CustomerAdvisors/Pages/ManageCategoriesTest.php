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
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\ManageCategories;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Ai\Models\CustomerAdvisorCategory;
use AdvisingApp\Ai\Tests\RequestFactories\CustomerAdvisorCategoryRequestFactory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Features\RenameQnaAdvisorsFeature;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;

test('Create Customer Advisor Category is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $customerAdvisor = CustomerAdvisor::factory()->create();

    actingAs($user)
        ->get(
            CustomerAdvisorResource::getUrl('manage-categories', [
                'record' => $customerAdvisor,
            ])
        )
        ->assertForbidden();

    livewire(ManageCategories::class, ['record' => $customerAdvisor->getKey()])
        ->assertForbidden();

    $user->givePermissionTo(RenameQnaAdvisorsFeature::active() ? ['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.create'] : ['qna_advisor.view-any', 'qna_advisor.*.view', 'qna_advisor.create']);

    actingAs($user)
        ->get(
            CustomerAdvisorResource::getUrl('manage-categories', [
                'record' => $customerAdvisor,
            ])
        )->assertSuccessful();
});

test('can create Customer Advisor Category', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $customerAdvisor = CustomerAdvisor::factory()->create();

    $user->givePermissionTo(RenameQnaAdvisorsFeature::active() ? ['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.create'] : ['qna_advisor.view-any', 'qna_advisor.*.view', 'qna_advisor.create']);

    actingAs($user);

    $customerAdvisorCategory = collect(CustomerAdvisorCategoryRequestFactory::new()->create());

    livewire(ManageCategories::class, ['record' => $customerAdvisor->getKey()])
        ->callTableAction('create', data: $customerAdvisorCategory->toArray())
        ->assertHasNoTableActionErrors();

    assertCount(1, CustomerAdvisorCategory::all());

    assertDatabaseHas(
        CustomerAdvisorCategory::class,
        $customerAdvisorCategory->toArray()
    );
});

test('Create Customer Advisor Category validates the inputs', function (array $data, array $errors) {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $customerAdvisor = CustomerAdvisor::factory()->create();

    actingAs($user)
        ->get(
            CustomerAdvisorResource::getUrl('manage-categories', [
                'record' => $customerAdvisor,
            ])
        )
        ->assertForbidden();

    livewire(ManageCategories::class, ['record' => $customerAdvisor->getKey()])
        ->assertForbidden();

    $user->givePermissionTo(RenameQnaAdvisorsFeature::active() ? ['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.create'] : ['qna_advisor.view-any', 'qna_advisor.*.view', 'qna_advisor.create']);

    actingAs($user)
        ->get(
            CustomerAdvisorResource::getUrl('manage-categories', [
                'record' => $customerAdvisor,
            ])
        )->assertSuccessful();

    $customerAdvisorCategory = collect(CustomerAdvisorCategoryRequestFactory::new($data)->create());

    livewire(ManageCategories::class, ['record' => $customerAdvisor->getKey()])
        ->callTableAction('create', data: $customerAdvisorCategory->toArray())
        ->assertHasTableActionErrors($errors);

    assertDatabaseMissing(
        CustomerAdvisorCategory::class,
        $customerAdvisorCategory->toArray()
    );
})->with(
    [
        'name required' => [
            CustomerAdvisorCategoryRequestFactory::new()->without('name'),
            ['name' => 'required'],
        ],
        'name string' => [
            CustomerAdvisorCategoryRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            CustomerAdvisorCategoryRequestFactory::new()->state(['name' => str()->random(257)]),
            ['name' => 'max'],
        ],
        'description required' => [
            CustomerAdvisorCategoryRequestFactory::new()->without('description'),
            ['description' => 'required'],
        ],
        'description max' => [
            CustomerAdvisorCategoryRequestFactory::new()->state(['description' => str()->random(65537)]),
            ['description' => 'max'],
        ],
    ]
);

test('can edit Customer Advisor Category', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo(RenameQnaAdvisorsFeature::active() ? ['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.*.update'] : ['qna_advisor.view-any', 'qna_advisor.*.view', 'qna_advisor.*.update']);

    $customerAdvisor = CustomerAdvisor::factory()->create();
    // TODO: Cleanup Task - During RenameQnaAdvisorsFeature cleanup, the state can be defined inline again
    $state = RenameQnaAdvisorsFeature::active() ? ['customer_advisor_id' => $customerAdvisor->getKey()] : ['qna_advisor_id' => $customerAdvisor->getKey()];
    $customerAdvisorCategory = CustomerAdvisorCategory::factory()->state($state)->create();

    $request = collect(CustomerAdvisorCategoryRequestFactory::new()->create());

    actingAs($user);

    livewire(ManageCategories::class, ['record' => $customerAdvisor->getKey()])
        ->callTableAction('edit', record: $customerAdvisorCategory->getKey(), data: $request->toArray())
        ->assertHasNoTableActionErrors();

    assertDatabaseHas(
        CustomerAdvisorCategory::class,
        $request->toArray()
    );
});

test('Edit Customer Advisor Category validates the inputs', function (array $data, array $errors) {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo(RenameQnaAdvisorsFeature::active() ? ['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.*.update'] : ['qna_advisor.view-any', 'qna_advisor.*.view', 'qna_advisor.*.update']);

    $customerAdvisor = CustomerAdvisor::factory()->create();

    // TODO: Cleanup Task - During RenameQnaAdvisorsFeature cleanup, the state can be defined inline again
    $state = RenameQnaAdvisorsFeature::active() ?
        [
            'name' => 'Education',
            'customer_advisor_id' => $customerAdvisor->getKey(),
        ] :
        [
            'name' => 'Education',
            'qna_advisor_id' => $customerAdvisor->getKey(),
        ];
    CustomerAdvisorCategory::factory()->state($state)->create();

    // TODO: Cleanup Task - During RenameQnaAdvisorsFeature cleanup, the state can be defined inline again
    $state = RenameQnaAdvisorsFeature::active() ? ['customer_advisor_id' => $customerAdvisor->getKey()] : ['qna_advisor_id' => $customerAdvisor->getKey()];
    $customerAdvisorCategory = CustomerAdvisorCategory::factory()->state($state)->create();

    $request = CustomerAdvisorCategoryRequestFactory::new($data)->create();

    actingAs($user);

    livewire(ManageCategories::class, ['record' => $customerAdvisor->getKey()])
        ->callTableAction('edit', record: $customerAdvisorCategory->getKey(), data: $request)
        ->assertHasTableActionErrors($errors);
})
    ->with(
        [
            'name required' => [
                CustomerAdvisorCategoryRequestFactory::new()->state(['name' => null]),
                ['name' => 'required'],
            ],
            'name string' => [
                CustomerAdvisorCategoryRequestFactory::new()->state(['name' => 1]),
                ['name' => 'string'],
            ],
            'name unique' => [
                CustomerAdvisorCategoryRequestFactory::new()->state(['name' => 'Education']),
                ['name' => 'unique'],
            ],
            'name max' => [
                CustomerAdvisorCategoryRequestFactory::new()->state(['name' => str()->random(257)]),
                ['name' => 'max'],
            ],
            'description required' => [
                CustomerAdvisorCategoryRequestFactory::new()->state(['description' => null]),
                ['description' => 'required'],
            ],
            'description max' => [
                CustomerAdvisorCategoryRequestFactory::new()->state(['description' => str()->random(65537)]),
                ['description' => 'max'],
            ],
        ]
    );
