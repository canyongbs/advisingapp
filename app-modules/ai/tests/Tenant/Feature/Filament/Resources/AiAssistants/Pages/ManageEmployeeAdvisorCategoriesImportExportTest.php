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
use AdvisingApp\Ai\Filament\Resources\AiAssistants\Pages\ManageEmployeeAdvisorCategories;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\EmployeeAdvisorCategory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\Export;
use App\Models\Import;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\ExportAction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->employeeAdvisors = true;
    $settings->save();
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

    expect($export)->not->toBeNull();
    expect($export->exporter)->toBe(EmployeeAdvisorCategoryExporter::class);

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

    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'employee-categories.csv';
    $import->file_path = 'imports/employee-categories.csv';
    $import->importer = EmployeeAdvisorCategoryImporter::class;
    $import->total_rows = 1;
    $import->save();

    $importer = app(EmployeeAdvisorCategoryImporter::class, [
        'import' => $import,
        'columnMap' => [
            'name' => 'name',
            'description' => 'description',
        ],
        'options' => [
            'employee_advisor_id' => $assistant->getKey(),
        ],
    ]);

    $importer([
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

it('validates employee advisor category import required and case-insensitive unique rules', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
    $assistant = AiAssistant::factory()->create();

    EmployeeAdvisorCategory::factory()->state([
        'employee_advisor_id' => $assistant->getKey(),
        'name' => 'Support',
    ])->create();

    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'employee-categories.csv';
    $import->file_path = 'imports/employee-categories.csv';
    $import->importer = EmployeeAdvisorCategoryImporter::class;
    $import->total_rows = 1;
    $import->save();

    $importer = app(EmployeeAdvisorCategoryImporter::class, [
        'import' => $import,
        'columnMap' => [
            'name' => 'name',
            'description' => 'description',
        ],
        'options' => [
            'employee_advisor_id' => $assistant->getKey(),
        ],
    ]);

    expect(fn () => $importer([
        'name' => 'Housing',
        'description' => null,
    ]))->toThrow(ValidationException::class);

    expect(fn () => $importer([
        'name' => 'support',
        'description' => 'Duplicate name for the same assistant.',
    ]))->toThrow(ValidationException::class);
});
