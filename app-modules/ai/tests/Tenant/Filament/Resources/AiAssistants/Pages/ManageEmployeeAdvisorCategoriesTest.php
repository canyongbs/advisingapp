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

use AdvisingApp\Ai\Filament\Exports\EmployeeAdvisorCategoryExporter;
use AdvisingApp\Ai\Filament\Imports\EmployeeAdvisorCategoryImporter;
use AdvisingApp\Ai\Filament\Resources\AiAssistants\AiAssistantResource;
use AdvisingApp\Ai\Filament\Resources\AiAssistants\Pages\ManageEmployeeAdvisorCategories;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\EmployeeAdvisorCategory;
use AdvisingApp\Ai\Tests\RequestFactories\EmployeeAdvisorCategoryRequestFactory;
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

beforeEach(function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->employeeAdvisors = true;
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

    $undoRepeaterFake = Repeater::fake();

    livewire(ManageEmployeeAdvisorCategories::class, ['record' => $assistant->getKey()])
        ->callTableAction('create', data: ['categories' => [$categoryData->toArray()]])
        ->assertHasNoTableActionErrors();

    $undoRepeaterFake();

    assertCount(1, EmployeeAdvisorCategory::all());

    assertDatabaseHas(
        EmployeeAdvisorCategory::class,
        $categoryData->toArray()
    );
});

test('can create multiple employee advisor categories at once', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $assistant = AiAssistant::factory()->create();

    $user->givePermissionTo(['assistant_custom.view-any', 'assistant_custom.*.view', 'assistant_custom.create']);

    actingAs($user);

    $firstCategory = collect(EmployeeAdvisorCategoryRequestFactory::new()->create(['name' => 'First Category']));
    $secondCategory = collect(EmployeeAdvisorCategoryRequestFactory::new()->create(['name' => 'Second Category']));

    $undoRepeaterFake = Repeater::fake();

    livewire(ManageEmployeeAdvisorCategories::class, ['record' => $assistant->getKey()])
        ->callTableAction('create', data: ['categories' => [$firstCategory->toArray(), $secondCategory->toArray()]])
        ->assertHasNoTableActionErrors();

    $undoRepeaterFake();

    assertCount(2, EmployeeAdvisorCategory::all());

    assertDatabaseHas(EmployeeAdvisorCategory::class, $firstCategory->toArray());
    assertDatabaseHas(EmployeeAdvisorCategory::class, $secondCategory->toArray());
});

test('creating duplicate employee advisor category names in the same batch is rejected', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $assistant = AiAssistant::factory()->create();

    $user->givePermissionTo(['assistant_custom.view-any', 'assistant_custom.*.view', 'assistant_custom.create']);

    actingAs($user);

    $firstCategory = collect(EmployeeAdvisorCategoryRequestFactory::new()->create());
    $secondCategory = collect(EmployeeAdvisorCategoryRequestFactory::new()->create(['name' => $firstCategory->get('name')]));

    $undoRepeaterFake = Repeater::fake();

    livewire(ManageEmployeeAdvisorCategories::class, ['record' => $assistant->getKey()])
        ->callTableAction('create', data: ['categories' => [$firstCategory->toArray(), $secondCategory->toArray()]])
        ->assertHasTableActionErrors(['categories.1.name' => 'The name field has a duplicate value.']);

    $undoRepeaterFake();

    assertCount(0, EmployeeAdvisorCategory::all());
});

test('creating an employee advisor category validates the inputs', function (EmployeeAdvisorCategoryRequestFactory $data, array $errors) {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $assistant = AiAssistant::factory()->create();

    $user->givePermissionTo(['assistant_custom.view-any', 'assistant_custom.*.view', 'assistant_custom.create']);

    actingAs($user);

    $categoryData = collect(EmployeeAdvisorCategoryRequestFactory::new($data)->create());

    $undoRepeaterFake = Repeater::fake();

    livewire(ManageEmployeeAdvisorCategories::class, ['record' => $assistant->getKey()])
        ->callTableAction('create', data: ['categories' => [$categoryData->toArray()]])
        ->assertHasTableActionErrors(collect($errors)->mapWithKeys(fn (string $rule, string $field) => ["categories.0.{$field}" => $rule])->toArray());

    $undoRepeaterFake();

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
            'name unique case insensitive' => [
                EmployeeAdvisorCategoryRequestFactory::new()->state(['name' => 'education']),
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

function employeeCategoryImporter(User $user, AiAssistant $assistant): EmployeeAdvisorCategoryImporter
{
    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'employee-categories.csv';
    $import->file_path = 'imports/employee-categories.csv';
    $import->importer = EmployeeAdvisorCategoryImporter::class;
    $import->total_rows = 1;
    $import->save();

    return app(EmployeeAdvisorCategoryImporter::class, [
        'import' => $import,
        'columnMap' => [
            'name' => 'name',
            'description' => 'description',
        ],
        'options' => [
            'employee_advisor_id' => $assistant->getKey(),
        ],
    ]);
}

describe('import and export', function () {
    it('shows the `ImportAction` and `ExportAction` actions', function () {
        $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
        $user->givePermissionTo(['assistant_custom.view-any', 'assistant_custom.*.view', 'assistant_custom.create']);

        $assistant = AiAssistant::factory()->create();

        actingAs($user);

        livewire(ManageEmployeeAdvisorCategories::class, ['record' => $assistant->getKey()])
            ->assertTableActionVisible(ImportAction::class)
            ->assertTableActionVisible(ExportAction::class);
    });

    it('exports employee advisor categories as scoped csv content', function () {
        Storage::fake('s3');

        config()->set('filament.default_filesystem_disk', 's3');
        config()->set('queue.default', 'sync');

        $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
        $user->givePermissionTo(['assistant_custom.view-any', 'assistant_custom.*.view']);

        $assistant = AiAssistant::factory()->create();
        $otherAssistant = AiAssistant::factory()->create();

        EmployeeAdvisorCategory::factory()->state([
            'employee_advisor_id' => $assistant->getKey(),
            'name' => 'Help Desk',
            'description' => 'Employee help desk guidance',
        ])->create();

        EmployeeAdvisorCategory::factory()->state([
            'employee_advisor_id' => $otherAssistant->getKey(),
            'name' => 'Payroll',
            'description' => 'Payroll related prompts',
        ])->create();

        actingAs($user);

        livewire(ManageEmployeeAdvisorCategories::class, ['record' => $assistant->getKey()])
            ->callTableAction(ExportAction::class)
            ->assertNotified();

        $export = Export::query()->latest()->first();

        expect($export)->not->toBeNull()
            ->and($export->exporter)->toBe(EmployeeAdvisorCategoryExporter::class);

        $disk = Storage::disk($export->file_disk);
        $files = collect($disk->files($export->getFileDirectory()))->sort()->values();
        $content = $files->map(fn (string $file): string => (string) $disk->get($file))->implode('');

        expect($content)
            ->toContain('Help Desk')
            ->toContain('Employee help desk guidance')
            ->not->toContain('Payroll')
            ->not->toContain('Payroll related prompts');
    });

    it('imports employee advisor categories scoped to the selected assistant', function () {
        $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

        $assistant = AiAssistant::factory()->create();
        $otherAssistant = AiAssistant::factory()->create();

        assertDatabaseMissing(EmployeeAdvisorCategory::class, ['name' => 'Knowledge Base']);

        employeeCategoryImporter($user, $assistant)([
            'name' => 'Knowledge Base',
            'description' => 'Internal article support responses.',
        ]);

        assertDatabaseHas(EmployeeAdvisorCategory::class, [
            'employee_advisor_id' => $assistant->getKey(),
            'name' => 'Knowledge Base',
            'description' => 'Internal article support responses.',
        ]);

        assertDatabaseMissing(EmployeeAdvisorCategory::class, [
            'employee_advisor_id' => $otherAssistant->getKey(),
            'name' => 'Knowledge Base',
        ]);
    });

    it('validates required fields during employee advisor category import', function () {
        $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
        $assistant = AiAssistant::factory()->create();

        expect(fn () => employeeCategoryImporter($user, $assistant)([
            'name' => 'Housing',
            'description' => null,
        ]))->toThrow(ValidationException::class);
    });

    it('validates employee advisor category import uniqueness case-insensitively', function () {
        $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
        $assistant = AiAssistant::factory()->create();

        EmployeeAdvisorCategory::factory()->state([
            'employee_advisor_id' => $assistant->getKey(),
            'name' => 'Support',
        ])->create();

        expect(fn () => employeeCategoryImporter($user, $assistant)([
            'name' => 'support',
            'description' => 'Duplicate name for the same assistant.',
        ]))->toThrow(ValidationException::class);
    });

    it('imports a name freed by a soft deleted category', function () {
        $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

        $assistant = AiAssistant::factory()->create();

        $category = EmployeeAdvisorCategory::factory()->state([
            'employee_advisor_id' => $assistant->getKey(),
            'name' => 'Help Desk',
        ])->create();

        $category->delete();

        employeeCategoryImporter($user, $assistant)([
            'name' => 'Help Desk',
            'description' => 'Recreated after the original was soft deleted.',
        ]);

        assertDatabaseHas(EmployeeAdvisorCategory::class, [
            'employee_advisor_id' => $assistant->getKey(),
            'name' => 'Help Desk',
            'description' => 'Recreated after the original was soft deleted.',
            'deleted_at' => null,
        ]);
    });
});

describe('authorization', function () {
    it('hides the `ImportAction` action without the `assistant_custom.create` permission', function () {
        $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
        $user->givePermissionTo(['assistant_custom.view-any', 'assistant_custom.*.view']);

        $assistant = AiAssistant::factory()->create();

        actingAs($user);

        livewire(ManageEmployeeAdvisorCategories::class, ['record' => $assistant->getKey()])
            ->assertTableActionHidden(ImportAction::class)
            ->assertTableActionVisible(ExportAction::class);
    });
});
