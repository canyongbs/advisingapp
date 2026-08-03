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
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\ManageCategories;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Ai\Models\CustomerAdvisorCategory;
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
    $settings->data->addons->customerAdvisors = true;
    $settings->save();
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

    expect($export)->not->toBeNull();
    expect($export->exporter)->toBe(CustomerAdvisorCategoryExporter::class);

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

    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'customer-categories.csv';
    $import->file_path = 'imports/customer-categories.csv';
    $import->importer = CustomerAdvisorCategoryImporter::class;
    $import->total_rows = 1;
    $import->save();

    $importer = app(CustomerAdvisorCategoryImporter::class, [
        'import' => $import,
        'columnMap' => [
            'name' => 'name',
            'description' => 'description',
        ],
        'options' => [
            'customer_advisor_id' => $advisor->getKey(),
        ],
    ]);

    $importer([
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

    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'customer-categories.csv';
    $import->file_path = 'imports/customer-categories.csv';
    $import->importer = CustomerAdvisorCategoryImporter::class;
    $import->total_rows = 1;
    $import->save();

    $importer = app(CustomerAdvisorCategoryImporter::class, [
        'import' => $import,
        'columnMap' => [
            'name' => 'name',
            'description' => 'description',
        ],
        'options' => [
            'customer_advisor_id' => $advisor->getKey(),
        ],
    ]);

    expect(fn () => $importer([
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

    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'customer-categories.csv';
    $import->file_path = 'imports/customer-categories.csv';
    $import->importer = CustomerAdvisorCategoryImporter::class;
    $import->total_rows = 1;
    $import->save();

    $scopedImporter = app(CustomerAdvisorCategoryImporter::class, [
        'import' => $import,
        'columnMap' => [
            'name' => 'name',
            'description' => 'description',
        ],
        'options' => [
            'customer_advisor_id' => $advisor->getKey(),
        ],
    ]);

    $scopedImporter([
        'name' => 'sales',
        'description' => 'Allowed because duplicate exists on another advisor.',
    ]);

    CustomerAdvisorCategory::factory()->state([
        'customer_advisor_id' => $advisor->getKey(),
        'name' => 'Support',
    ])->create();

    expect(fn () => $scopedImporter([
        'name' => 'support',
        'description' => 'Should fail as duplicate on the same advisor.',
    ]))->toThrow(ValidationException::class);
});
