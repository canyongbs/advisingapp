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

use AdvisingApp\Ai\Filament\Resources\AiAssistants\AiAssistantResource;
use AdvisingApp\Ai\Filament\Resources\AiAssistants\Pages\ManageEmployeeAdvisorCategories;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\EmployeeAdvisorCategory;
use AdvisingApp\Ai\Tests\RequestFactories\EmployeeAdvisorCategoryRequestFactory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Features\EmployeeAdvisorQnaFeature;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;

beforeEach(function () {
    EmployeeAdvisorQnaFeature::activate();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->customAiAssistants = true;
    $settings->save();
});

test('creating an employee advisor category is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $assistant = AiAssistant::factory()->create();

    actingAs($user)
        ->get(
            AiAssistantResource::getUrl('manage-categories', [
                'record' => $assistant,
            ])
        )
        ->assertForbidden();

    livewire(ManageEmployeeAdvisorCategories::class, ['record' => $assistant->getKey()])
        ->assertForbidden();

    $user->givePermissionTo(['assistant_custom.view-any', 'assistant_custom.*.view', 'assistant_custom.create']);

    actingAs($user)
        ->get(
            AiAssistantResource::getUrl('manage-categories', [
                'record' => $assistant,
            ])
        )->assertSuccessful();
});

test('can create an employee advisor category', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $assistant = AiAssistant::factory()->create();

    $user->givePermissionTo(['assistant_custom.view-any', 'assistant_custom.*.view', 'assistant_custom.create']);

    actingAs($user);

    $categoryData = collect(EmployeeAdvisorCategoryRequestFactory::new()->create());

    livewire(ManageEmployeeAdvisorCategories::class, ['record' => $assistant->getKey()])
        ->callTableAction('create', data: $categoryData->toArray())
        ->assertHasNoTableActionErrors();

    assertCount(1, EmployeeAdvisorCategory::all());

    assertDatabaseHas(
        EmployeeAdvisorCategory::class,
        $categoryData->toArray()
    );
});

test('creating an employee advisor category validates the inputs', function (EmployeeAdvisorCategoryRequestFactory $data, array $errors) {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $assistant = AiAssistant::factory()->create();

    $user->givePermissionTo(['assistant_custom.view-any', 'assistant_custom.*.view', 'assistant_custom.create']);

    actingAs($user);

    $categoryData = collect(EmployeeAdvisorCategoryRequestFactory::new($data)->create());

    livewire(ManageEmployeeAdvisorCategories::class, ['record' => $assistant->getKey()])
        ->callTableAction('create', data: $categoryData->toArray())
        ->assertHasTableActionErrors($errors);

    assertDatabaseMissing(
        EmployeeAdvisorCategory::class,
        $categoryData->toArray()
    );
})->with(
    [
        'name required' => [
            EmployeeAdvisorCategoryRequestFactory::new()->without('name'),
            ['name' => 'required'],
        ],
        'name string' => [
            EmployeeAdvisorCategoryRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            EmployeeAdvisorCategoryRequestFactory::new()->state(['name' => str()->random(257)]),
            ['name' => 'max'],
        ],
        'description required' => [
            EmployeeAdvisorCategoryRequestFactory::new()->without('description'),
            ['description' => 'required'],
        ],
        'description max' => [
            EmployeeAdvisorCategoryRequestFactory::new()->state(['description' => str()->random(65537)]),
            ['description' => 'max'],
        ],
    ]
);

test('can edit an employee advisor category', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo([
        'assistant_custom.view-any',
        'assistant_custom.*.view',
        'assistant_custom.*.update',
    ]);

    $assistant = AiAssistant::factory()->create();
    $category = EmployeeAdvisorCategory::factory()->state([
        'employee_advisor_id' => $assistant->getKey(),
    ])->create();

    $request = collect(EmployeeAdvisorCategoryRequestFactory::new()->create());

    actingAs($user);

    livewire(ManageEmployeeAdvisorCategories::class, ['record' => $assistant->getKey()])
        ->callTableAction('edit', record: $category->getKey(), data: $request->toArray())
        ->assertHasNoTableActionErrors();

    assertDatabaseHas(
        EmployeeAdvisorCategory::class,
        $request->toArray()
    );
});

test('editing an employee advisor category validates the inputs', function (EmployeeAdvisorCategoryRequestFactory $data, array $errors) {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo([
        'assistant_custom.view-any',
        'assistant_custom.*.view',
        'assistant_custom.*.update',
    ]);

    $assistant = AiAssistant::factory()->create();

    EmployeeAdvisorCategory::factory()->state([
        'name' => 'Education',
        'employee_advisor_id' => $assistant->getKey(),
    ])->create();

    $category = EmployeeAdvisorCategory::factory()->state([
        'employee_advisor_id' => $assistant->getKey(),
    ])->create();

    $request = EmployeeAdvisorCategoryRequestFactory::new($data)->create();

    actingAs($user);

    livewire(ManageEmployeeAdvisorCategories::class, ['record' => $assistant->getKey()])
        ->callTableAction('edit', record: $category->getKey(), data: $request)
        ->assertHasTableActionErrors($errors);
})
    ->with(
        [
            'name required' => [
                EmployeeAdvisorCategoryRequestFactory::new()->state(['name' => null]),
                ['name' => 'required'],
            ],
            'name string' => [
                EmployeeAdvisorCategoryRequestFactory::new()->state(['name' => 1]),
                ['name' => 'string'],
            ],
            'name unique' => [
                EmployeeAdvisorCategoryRequestFactory::new()->state(['name' => 'Education']),
                ['name' => 'unique'],
            ],
            'name max' => [
                EmployeeAdvisorCategoryRequestFactory::new()->state(['name' => str()->random(257)]),
                ['name' => 'max'],
            ],
            'description required' => [
                EmployeeAdvisorCategoryRequestFactory::new()->state(['description' => null]),
                ['description' => 'required'],
            ],
            'description max' => [
                EmployeeAdvisorCategoryRequestFactory::new()->state(['description' => str()->random(65537)]),
                ['description' => 'max'],
            ],
        ]
    );
