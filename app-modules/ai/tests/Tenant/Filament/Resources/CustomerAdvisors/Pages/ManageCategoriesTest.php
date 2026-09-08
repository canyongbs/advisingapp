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

use AdvisingApp\Ai\Filament\Exports\CustomerAdvisorCategoryExporter;
use AdvisingApp\Ai\Filament\Imports\CustomerAdvisorCategoryImporter;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\CustomerAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\ManageCategories;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Ai\Models\CustomerAdvisorCategory;
use AdvisingApp\Ai\Tests\RequestFactories\CustomerAdvisorCategoryRequestFactory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\Export;
use App\Models\Import;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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

    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.create']);

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

    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.create']);

    actingAs($user);

    $customerAdvisorCategory = collect(CustomerAdvisorCategoryRequestFactory::new()->create());

    $undoRepeaterFake = Repeater::fake();

    livewire(ManageCategories::class, ['record' => $customerAdvisor->getKey()])
        ->callTableAction('create', data: ['categories' => [$customerAdvisorCategory->toArray()]])
        ->assertHasNoTableActionErrors();

    $undoRepeaterFake();

    assertCount(1, CustomerAdvisorCategory::all());

    assertDatabaseHas(
        CustomerAdvisorCategory::class,
        $customerAdvisorCategory->toArray()
    );
});

test('can create multiple Customer Advisor Categories at once', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $customerAdvisor = CustomerAdvisor::factory()->create();

    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.create']);

    actingAs($user);

    $firstCategory = collect(CustomerAdvisorCategoryRequestFactory::new()->create(['name' => 'First Category']));
    $secondCategory = collect(CustomerAdvisorCategoryRequestFactory::new()->create(['name' => 'Second Category']));

    $undoRepeaterFake = Repeater::fake();

    livewire(ManageCategories::class, ['record' => $customerAdvisor->getKey()])
        ->callTableAction('create', data: ['categories' => [$firstCategory->toArray(), $secondCategory->toArray()]])
        ->assertHasNoTableActionErrors();

    $undoRepeaterFake();

    assertCount(2, CustomerAdvisorCategory::all());

    assertDatabaseHas(CustomerAdvisorCategory::class, $firstCategory->toArray());
    assertDatabaseHas(CustomerAdvisorCategory::class, $secondCategory->toArray());
});

test('creating duplicate Customer Advisor Category names in the same batch is rejected', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $customerAdvisor = CustomerAdvisor::factory()->create();

    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.create']);

    actingAs($user);

    $firstCategory = collect(CustomerAdvisorCategoryRequestFactory::new()->create());
    $secondCategory = collect(CustomerAdvisorCategoryRequestFactory::new()->create(['name' => $firstCategory->get('name')]));

    $undoRepeaterFake = Repeater::fake();

    livewire(ManageCategories::class, ['record' => $customerAdvisor->getKey()])
        ->callTableAction('create', data: ['categories' => [$firstCategory->toArray(), $secondCategory->toArray()]])
        ->assertHasTableActionErrors(['categories.1.name' => 'The name field has a duplicate value.']);

    $undoRepeaterFake();

    assertCount(0, CustomerAdvisorCategory::all());
});

test('Create Customer Advisor Category validates the inputs', function (CustomerAdvisorCategoryRequestFactory $data, array $errors) {
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

    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.create']);

    actingAs($user)
        ->get(
            CustomerAdvisorResource::getUrl('manage-categories', [
                'record' => $customerAdvisor,
            ])
        )->assertSuccessful();

    $customerAdvisorCategory = collect(CustomerAdvisorCategoryRequestFactory::new($data)->create());

    $undoRepeaterFake = Repeater::fake();

    livewire(ManageCategories::class, ['record' => $customerAdvisor->getKey()])
        ->callTableAction('create', data: ['categories' => [$customerAdvisorCategory->toArray()]])
        ->assertHasTableActionErrors(collect($errors)->mapWithKeys(fn (string $rule, string $field) => ["categories.0.{$field}" => $rule])->toArray());

    $undoRepeaterFake();

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

    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.*.update']);

    $customerAdvisor = CustomerAdvisor::factory()->create();
    $customerAdvisorCategory = CustomerAdvisorCategory::factory()->state(['customer_advisor_id' => $customerAdvisor->getKey()])->create();

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

test('Edit Customer Advisor Category validates the inputs', function (CustomerAdvisorCategoryRequestFactory $data, array $errors) {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.*.update']);

    $customerAdvisor = CustomerAdvisor::factory()->create();

    CustomerAdvisorCategory::factory()->state([
        'name' => 'Education',
        'customer_advisor_id' => $customerAdvisor->getKey(),
    ])->create();

    $customerAdvisorCategory = CustomerAdvisorCategory::factory()->state(['customer_advisor_id' => $customerAdvisor->getKey()])->create();

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
            'name unique case insensitive' => [
                CustomerAdvisorCategoryRequestFactory::new()->state(['name' => 'education']),
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

function customerCategoryImporter(User $user, CustomerAdvisor $advisor): CustomerAdvisorCategoryImporter
{
    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'customer-categories.csv';
    $import->file_path = 'imports/customer-categories.csv';
    $import->importer = CustomerAdvisorCategoryImporter::class;
    $import->total_rows = 1;
    $import->save();

    return app(CustomerAdvisorCategoryImporter::class, [
        'import' => $import,
        'columnMap' => [
            'name' => 'name',
            'description' => 'description',
        ],
        'options' => [
            'customer_advisor_id' => $advisor->getKey(),
        ],
    ]);
}

describe('import and export', function () {
    beforeEach(function () {
        $settings = app(LicenseSettings::class);

        $settings->data->addons->customerAdvisors = true;

        $settings->save();
    });

    it('shows the `ImportAction` and `ExportAction` actions', function () {
        $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
        $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.create']);

        $customerAdvisor = CustomerAdvisor::factory()->create();

        actingAs($user);

        livewire(ManageCategories::class, ['record' => $customerAdvisor->getKey()])
            ->assertTableActionVisible(ImportAction::class)
            ->assertTableActionVisible(ExportAction::class);
    });

    it('exports customer advisor categories as scoped csv content', function () {
        Storage::fake('s3');

        config()->set('filament.default_filesystem_disk', 's3');
        config()->set('queue.default', 'sync');

        $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
        $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.*.view']);

        $advisor = CustomerAdvisor::factory()->create();
        $otherAdvisor = CustomerAdvisor::factory()->create();

        CustomerAdvisorCategory::factory()->state([
            'customer_advisor_id' => $advisor->getKey(),
            'name' => 'Admissions',
            'description' => 'Admissions FAQs',
        ])->create();

        CustomerAdvisorCategory::factory()->state([
            'customer_advisor_id' => $otherAdvisor->getKey(),
            'name' => 'Billing',
            'description' => 'Billing FAQs',
        ])->create();

        actingAs($user);

        livewire(ManageCategories::class, ['record' => $advisor->getKey()])
            ->callTableAction(ExportAction::class)
            ->assertNotified();

        $export = Export::query()->latest()->first();

        expect($export)->not->toBeNull()
            ->and($export->exporter)->toBe(CustomerAdvisorCategoryExporter::class);

        $disk = Storage::disk($export->file_disk);
        $files = collect($disk->files($export->getFileDirectory()))->sort()->values();
        $content = $files->map(fn (string $file): string => (string) $disk->get($file))->implode('');

        expect($content)
            ->toContain('Admissions')
            ->toContain('Admissions FAQs')
            ->not->toContain('Billing')
            ->not->toContain('Billing FAQs');
    });

    it('imports customer advisor categories scoped to the selected advisor', function () {
        $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

        $advisor = CustomerAdvisor::factory()->create();
        $otherAdvisor = CustomerAdvisor::factory()->create();

        assertDatabaseMissing(CustomerAdvisorCategory::class, ['name' => 'Scholarships']);

        customerCategoryImporter($user, $advisor)([
            'name' => 'Scholarships',
            'description' => 'Scholarship and aid guidance.',
        ]);

        assertDatabaseHas(CustomerAdvisorCategory::class, [
            'customer_advisor_id' => $advisor->getKey(),
            'name' => 'Scholarships',
            'description' => 'Scholarship and aid guidance.',
        ]);

        assertDatabaseMissing(CustomerAdvisorCategory::class, [
            'customer_advisor_id' => $otherAdvisor->getKey(),
            'name' => 'Scholarships',
        ]);
    });

    it('validates required fields during customer advisor category import', function () {
        $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
        $advisor = CustomerAdvisor::factory()->create();

        expect(fn () => customerCategoryImporter($user, $advisor)([
            'name' => 'Housing',
            'description' => null,
        ]))->toThrow(ValidationException::class);
    });

    it('validates customer advisor category import uniqueness case-insensitively and scoped per advisor', function () {
        $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

        $advisor = CustomerAdvisor::factory()->create();
        $otherAdvisor = CustomerAdvisor::factory()->create();

        CustomerAdvisorCategory::factory()->state([
            'customer_advisor_id' => $otherAdvisor->getKey(),
            'name' => 'Sales',
        ])->create();

        $importer = customerCategoryImporter($user, $advisor);

        $importer([
            'name' => 'sales',
            'description' => 'Allowed because duplicate exists on another advisor.',
        ]);

        CustomerAdvisorCategory::factory()->state([
            'customer_advisor_id' => $advisor->getKey(),
            'name' => 'Support',
        ])->create();

        expect(fn () => $importer([
            'name' => 'support',
            'description' => 'Should fail as duplicate on the same advisor.',
        ]))->toThrow(ValidationException::class);
    });

    it('imports a name freed by a soft deleted category', function () {
        $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

        $advisor = CustomerAdvisor::factory()->create();

        $category = CustomerAdvisorCategory::factory()->state([
            'customer_advisor_id' => $advisor->getKey(),
            'name' => 'Admissions',
        ])->create();

        $category->delete();

        customerCategoryImporter($user, $advisor)([
            'name' => 'Admissions',
            'description' => 'Recreated after the original was soft deleted.',
        ]);

        assertDatabaseHas(CustomerAdvisorCategory::class, [
            'customer_advisor_id' => $advisor->getKey(),
            'name' => 'Admissions',
            'description' => 'Recreated after the original was soft deleted.',
            'deleted_at' => null,
        ]);
    });
});

describe('authorization', function () {
    beforeEach(function () {
        $settings = app(LicenseSettings::class);

        $settings->data->addons->customerAdvisors = true;

        $settings->save();
    });

    it('hides the `ImportAction` action without the `customer_advisor.create` permission', function () {
        $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
        $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.*.view']);

        $customerAdvisor = CustomerAdvisor::factory()->create();

        actingAs($user);

        livewire(ManageCategories::class, ['record' => $customerAdvisor->getKey()])
            ->assertTableActionHidden(ImportAction::class)
            ->assertTableActionVisible(ExportAction::class);
    });
});
