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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AdvisingApp\Ai\Filament\Exports\CustomerAdvisorQuestionExporter;
use AdvisingApp\Ai\Filament\Imports\CustomerAdvisorQuestionImporter;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\ManageCustomerQuestions;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Ai\Models\CustomerAdvisorCategory;
use AdvisingApp\Ai\Models\CustomerAdvisorQuestion;
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

it('exports customer advisor questions as scoped csv content with category name', function () {
    Storage::fake('s3');

    config()->set('filament.default_filesystem_disk', 's3');
    config()->set('queue.default', 'sync');

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.*.view']);

    $advisor = CustomerAdvisor::factory()->create();
    $otherAdvisor = CustomerAdvisor::factory()->create();

    $category = CustomerAdvisorCategory::factory()->state([
        'customer_advisor_id' => $advisor->getKey(),
        'name' => 'Admissions',
    ])->create();

    $otherCategory = CustomerAdvisorCategory::factory()->state([
        'customer_advisor_id' => $otherAdvisor->getKey(),
        'name' => 'Billing',
    ])->create();

    CustomerAdvisorQuestion::factory()->state([
        'category_id' => $category->getKey(),
        'question' => 'How do I apply?',
        'answer' => 'Apply online at our website.',
    ])->create();

    CustomerAdvisorQuestion::factory()->state([
        'category_id' => $otherCategory->getKey(),
        'question' => 'What is your billing process?',
        'answer' => 'We bill monthly.',
    ])->create();

    actingAs($user);

    livewire(ManageCustomerQuestions::class, ['record' => $advisor->getKey()])
        ->callTableAction(ExportAction::class)
        ->assertNotified();

    $export = Export::query()->latest()->first();

    expect($export)->not->toBeNull();
    expect($export->exporter)->toBe(CustomerAdvisorQuestionExporter::class);

    $disk = Storage::disk($export->file_disk);
    $files = collect($disk->files($export->getFileDirectory()))->sort()->values();
    $content = $files->map(fn (string $file): string => (string) $disk->get($file))->implode('');

    expect($content)
        ->toContain('How do I apply?')
        ->toContain('Apply online at our website.')
        ->toContain('Admissions')
        ->not->toContain('What is your billing process?')
        ->not->toContain('We bill monthly.')
        ->not->toContain('Billing');
});

it('imports customer advisor questions scoped to the selected advisor with case-insensitive category matching', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $advisor = CustomerAdvisor::factory()->create();
    $otherAdvisor = CustomerAdvisor::factory()->create();

    $category = CustomerAdvisorCategory::factory()->state([
        'customer_advisor_id' => $advisor->getKey(),
        'name' => 'Admissions',
    ])->create();

    CustomerAdvisorCategory::factory()->state([
        'customer_advisor_id' => $otherAdvisor->getKey(),
        'name' => 'Admissions',
    ])->create();

    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'customer-questions.csv';
    $import->file_path = 'imports/customer-questions.csv';
    $import->importer = CustomerAdvisorQuestionImporter::class;
    $import->total_rows = 1;
    $import->save();

    $importer = app(CustomerAdvisorQuestionImporter::class, [
        'import' => $import,
        'columnMap' => [
            'question' => 'question',
            'answer' => 'answer',
            'category' => 'category',
        ],
        'options' => [
            'customer_advisor_id' => $advisor->getKey(),
        ],
    ]);

    $importer([
        'question' => 'How do I apply?',
        'answer' => 'Apply online.',
        'category' => 'Admissions',
    ]);

    assertDatabaseHas(CustomerAdvisorQuestion::class, [
        'category_id' => $category->getKey(),
        'question' => 'How do I apply?',
        'answer' => 'Apply online.',
    ]);

    assertDatabaseMissing(CustomerAdvisorQuestion::class, [
        'category_id' => $category->getKey(),
        'question' => 'How do I apply?',
        'answer' => 'Apply online.',
        'customer_advisor_id' => $otherAdvisor->getKey(),
    ]);
});

it('fails customer advisor question import cleanly when category does not exist', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $advisor = CustomerAdvisor::factory()->create();

    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'customer-questions.csv';
    $import->file_path = 'imports/customer-questions.csv';
    $import->importer = CustomerAdvisorQuestionImporter::class;
    $import->total_rows = 1;
    $import->save();

    $importer = app(CustomerAdvisorQuestionImporter::class, [
        'import' => $import,
        'columnMap' => [
            'question' => 'question',
            'answer' => 'answer',
            'category' => 'category',
        ],
        'options' => [
            'customer_advisor_id' => $advisor->getKey(),
        ],
    ]);

    expect(fn () => $importer([
        'question' => 'How do I apply?',
        'answer' => 'Apply online.',
        'category' => 'NonexistentCategory',
    ]))->toThrow(\InvalidArgumentException::class);
});

it('validates required fields during customer advisor question import', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $advisor = CustomerAdvisor::factory()->create();

    CustomerAdvisorCategory::factory()->state([
        'customer_advisor_id' => $advisor->getKey(),
        'name' => 'Admissions',
    ])->create();

    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'customer-questions.csv';
    $import->file_path = 'imports/customer-questions.csv';
    $import->importer = CustomerAdvisorQuestionImporter::class;
    $import->total_rows = 1;
    $import->save();

    $importer = app(CustomerAdvisorQuestionImporter::class, [
        'import' => $import,
        'columnMap' => [
            'question' => 'question',
            'answer' => 'answer',
            'category' => 'category',
        ],
        'options' => [
            'customer_advisor_id' => $advisor->getKey(),
        ],
    ]);

    expect(fn () => $importer([
        'question' => null,
        'answer' => 'Apply online.',
        'category' => 'Admissions',
    ]))->toThrow(ValidationException::class);

    expect(fn () => $importer([
        'question' => 'How do I apply?',
        'answer' => null,
        'category' => 'Admissions',
    ]))->toThrow(ValidationException::class);

    expect(fn () => $importer([
        'question' => 'How do I apply?',
        'answer' => 'Apply online.',
        'category' => null,
    ]))->toThrow(\InvalidArgumentException::class);
});

it('shows import and export actions on customer questions page', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.create']);

    $advisor = CustomerAdvisor::factory()->create();

    actingAs($user);

    livewire(ManageCustomerQuestions::class, ['record' => $advisor->getKey()])
        ->assertTableActionVisible(ExportAction::class);
});
