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

use AdvisingApp\Ai\Filament\Exports\EmployeeAdvisorQuestionExporter;
use AdvisingApp\Ai\Filament\Imports\EmployeeAdvisorQuestionImporter;
use AdvisingApp\Ai\Filament\Resources\AiAssistants\AiAssistantResource;
use AdvisingApp\Ai\Filament\Resources\AiAssistants\Pages\ManageEmployeeAdvisorQuestions;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\EmployeeAdvisorCategory;
use AdvisingApp\Ai\Models\EmployeeAdvisorQuestion;
use AdvisingApp\Ai\Tests\RequestFactories\EmployeeAdvisorQuestionRequestFactory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\Export;
use App\Models\Import;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
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

test('creating an employee advisor question is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $assistant = AiAssistant::factory()->create();

    actingAs($user)
        ->get(
            AiAssistantResource::getUrl('manage-questions', [
                'record' => $assistant,
            ])
        )
        ->assertForbidden();

    livewire(ManageEmployeeAdvisorQuestions::class, ['record' => $assistant->getKey()])
        ->assertForbidden();

    $user->givePermissionTo([
        'assistant_custom.view-any',
        'assistant_custom.*.view',
        'assistant_custom.create',
    ]);

    actingAs($user)
        ->get(
            AiAssistantResource::getUrl('manage-questions', [
                'record' => $assistant,
            ])
        )->assertSuccessful();
});

test('can create an employee advisor question', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $assistant = AiAssistant::factory()->create();

    $user->givePermissionTo([
        'assistant_custom.view-any',
        'assistant_custom.*.view',
        'assistant_custom.create',
    ]);

    $questionData = collect(EmployeeAdvisorQuestionRequestFactory::new()->create());

    actingAs($user);

    $undoRepeaterFake = Repeater::fake();

    livewire(ManageEmployeeAdvisorQuestions::class, ['record' => $assistant->getKey()])
        ->callTableAction('create', data: ['questions' => [$questionData->toArray()]])
        ->assertHasNoTableActionErrors();

    $undoRepeaterFake();

    assertCount(1, EmployeeAdvisorQuestion::all());

    assertDatabaseHas(
        EmployeeAdvisorQuestion::class,
        $questionData->toArray()
    );
});

test('can create multiple employee advisor questions at once', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $assistant = AiAssistant::factory()->create();

    $user->givePermissionTo([
        'assistant_custom.view-any',
        'assistant_custom.*.view',
        'assistant_custom.create',
    ]);

    $firstQuestion = collect(EmployeeAdvisorQuestionRequestFactory::new()->create());
    $secondQuestion = collect(EmployeeAdvisorQuestionRequestFactory::new()->create());

    actingAs($user);

    $undoRepeaterFake = Repeater::fake();

    livewire(ManageEmployeeAdvisorQuestions::class, ['record' => $assistant->getKey()])
        ->callTableAction('create', data: ['questions' => [$firstQuestion->toArray(), $secondQuestion->toArray()]])
        ->assertHasNoTableActionErrors();

    $undoRepeaterFake();

    assertCount(2, EmployeeAdvisorQuestion::all());

    assertDatabaseHas(EmployeeAdvisorQuestion::class, $firstQuestion->toArray());
    assertDatabaseHas(EmployeeAdvisorQuestion::class, $secondQuestion->toArray());
});

test('creating an employee advisor question validates the inputs', function (EmployeeAdvisorQuestionRequestFactory $data, array $errors) {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo([
        'assistant_custom.view-any',
        'assistant_custom.*.view',
        'assistant_custom.create',
    ]);

    $assistant = AiAssistant::factory()->create();

    $questionData = collect(EmployeeAdvisorQuestionRequestFactory::new($data)->create());

    actingAs($user);

    $undoRepeaterFake = Repeater::fake();

    livewire(ManageEmployeeAdvisorQuestions::class, ['record' => $assistant->getKey()])
        ->callTableAction('create', data: ['questions' => [$questionData->toArray()]])
        ->assertHasTableActionErrors(collect($errors)->mapWithKeys(fn (string $rule, string $field) => ["questions.0.{$field}" => $rule])->toArray());

    $undoRepeaterFake();

    assertDatabaseMissing(
        EmployeeAdvisorQuestion::class,
        $questionData->toArray()
    );
})->with(
    [
        'category_id required' => [
            EmployeeAdvisorQuestionRequestFactory::new()->state(['category_id' => null]),
            ['category_id' => 'required'],
        ],
        'question required' => [
            EmployeeAdvisorQuestionRequestFactory::new()->state(['question' => null]),
            ['question' => 'required'],
        ],
        'question string' => [
            EmployeeAdvisorQuestionRequestFactory::new()->state(['question' => 1]),
            ['question' => 'string'],
        ],
        'question max' => [
            EmployeeAdvisorQuestionRequestFactory::new()->state(['question' => str()->random(257)]),
            ['question' => 'max'],
        ],
        'answer required' => [
            EmployeeAdvisorQuestionRequestFactory::new()->state(['answer' => null]),
            ['answer' => 'required'],
        ],
        'answer max' => [
            EmployeeAdvisorQuestionRequestFactory::new()->state(['answer' => str()->random(65537)]),
            ['answer' => 'max'],
        ],
    ]
);

test('can edit an employee advisor question', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo([
        'assistant_custom.view-any',
        'assistant_custom.*.view',
        'assistant_custom.*.update',
    ]);

    $assistant = AiAssistant::factory()->create();

    $question = EmployeeAdvisorQuestion::factory()->state([
        'category_id' => EmployeeAdvisorCategory::factory()->state([
            'employee_advisor_id' => $assistant->getKey(),
        ]),
    ])->create();

    $request = collect(EmployeeAdvisorQuestionRequestFactory::new()->create());

    actingAs($user);

    livewire(ManageEmployeeAdvisorQuestions::class, ['record' => $assistant->getKey()])
        ->callTableAction('edit', record: $question->getKey(), data: $request->toArray())
        ->assertHasNoTableActionErrors();

    assertDatabaseHas(
        EmployeeAdvisorQuestion::class,
        $request->toArray()
    );
});

test('editing an employee advisor question validates the inputs', function (EmployeeAdvisorQuestionRequestFactory $data, array $errors) {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo([
        'assistant_custom.view-any',
        'assistant_custom.*.view',
        'assistant_custom.*.update',
    ]);

    $assistant = AiAssistant::factory()->create();

    $question = EmployeeAdvisorQuestion::factory()->state([
        'category_id' => EmployeeAdvisorCategory::factory()->state([
            'employee_advisor_id' => $assistant->getKey(),
        ]),
    ])->create();

    $request = EmployeeAdvisorQuestionRequestFactory::new($data)->create();

    actingAs($user);

    livewire(ManageEmployeeAdvisorQuestions::class, ['record' => $assistant->getKey()])
        ->callTableAction('edit', record: $question->getKey(), data: $request)
        ->assertHasTableActionErrors($errors);
})
    ->with(
        [
            'category_id required' => [
                EmployeeAdvisorQuestionRequestFactory::new()->state(['category_id' => null]),
                ['category_id' => 'required'],
            ],
            'question required' => [
                EmployeeAdvisorQuestionRequestFactory::new()->state(['question' => null]),
                ['question' => 'required'],
            ],
            'question string' => [
                EmployeeAdvisorQuestionRequestFactory::new()->state(['question' => 1]),
                ['question' => 'string'],
            ],
            'question max' => [
                EmployeeAdvisorQuestionRequestFactory::new()->state(['question' => str()->random(257)]),
                ['question' => 'max'],
            ],
            'answer required' => [
                EmployeeAdvisorQuestionRequestFactory::new()->state(['answer' => null]),
                ['answer' => 'required'],
            ],
            'answer max' => [
                EmployeeAdvisorQuestionRequestFactory::new()->state(['answer' => str()->random(65537)]),
                ['answer' => 'max'],
            ],
        ]
    );

it('exports employee advisor questions as scoped csv content with category name', function () {
    Storage::fake('s3');

    config()->set('filament.default_filesystem_disk', 's3');
    config()->set('queue.default', 'sync');

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
    $user->givePermissionTo(['assistant_custom.view-any', 'assistant_custom.*.view']);

    $assistant = AiAssistant::factory()->create();
    $otherAssistant = AiAssistant::factory()->create();

    $category = EmployeeAdvisorCategory::factory()->state([
        'employee_advisor_id' => $assistant->getKey(),
        'name' => 'Knowledge Base',
    ])->create();

    $otherCategory = EmployeeAdvisorCategory::factory()->state([
        'employee_advisor_id' => $otherAssistant->getKey(),
        'name' => 'Policies',
    ])->create();

    EmployeeAdvisorQuestion::factory()->state([
        'category_id' => $category->getKey(),
        'question' => 'What is the password reset process?',
        'answer' => 'Go to login and click forgot password.',
    ])->create();

    EmployeeAdvisorQuestion::factory()->state([
        'category_id' => $otherCategory->getKey(),
        'question' => 'What is the vacation policy?',
        'answer' => '20 days per year.',
    ])->create();

    actingAs($user);

    livewire(ManageEmployeeAdvisorQuestions::class, ['record' => $assistant->getKey()])
        ->callTableAction(ExportAction::class)
        ->assertNotified();

    $export = Export::query()->latest()->first();

    expect($export)->not->toBeNull();
    expect($export->exporter)->toBe(EmployeeAdvisorQuestionExporter::class);

    $disk = Storage::disk($export->file_disk);
    $files = collect($disk->files($export->getFileDirectory()))->sort()->values();
    $content = $files->map(fn (string $file): string => (string) $disk->get($file))->implode('');

    expect($content)
        ->toContain('What is the password reset process?')
        ->toContain('Go to login and click forgot password.')
        ->toContain('Knowledge Base')
        ->not->toContain('What is the vacation policy?')
        ->not->toContain('20 days per year.')
        ->not->toContain('Policies');
});

it('imports employee advisor questions scoped to the selected assistant with case-insensitive category matching', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $assistant = AiAssistant::factory()->create();
    $otherAssistant = AiAssistant::factory()->create();

    $category = EmployeeAdvisorCategory::factory()->state([
        'employee_advisor_id' => $assistant->getKey(),
        'name' => 'Knowledge Base',
    ])->create();

    $otherCategory = EmployeeAdvisorCategory::factory()->state([
        'employee_advisor_id' => $otherAssistant->getKey(),
        'name' => 'Knowledge Base',
    ])->create();

    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'employee-questions.csv';
    $import->file_path = 'imports/employee-questions.csv';
    $import->importer = EmployeeAdvisorQuestionImporter::class;
    $import->total_rows = 1;
    $import->save();

    $importer = app(EmployeeAdvisorQuestionImporter::class, [
        'import' => $import,
        'columnMap' => [
            'question' => 'question',
            'answer' => 'answer',
            'category' => 'category',
        ],
        'options' => [
            'employee_advisor_id' => $assistant->getKey(),
        ],
    ]);

    $importer([
        'question' => 'What is the password reset process?',
        'answer' => 'Go to login and click forgot password.',
        'category' => 'knowledge base',
    ]);

    assertDatabaseHas(EmployeeAdvisorQuestion::class, [
        'category_id' => $category->getKey(),
        'question' => 'What is the password reset process?',
        'answer' => 'Go to login and click forgot password.',
    ]);

    assertDatabaseMissing(EmployeeAdvisorQuestion::class, [
        'category_id' => $otherCategory->getKey(),
        'question' => 'What is the password reset process?',
        'answer' => 'Go to login and click forgot password.',
    ]);
});

it('fails employee advisor question import cleanly when category does not exist', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $assistant = AiAssistant::factory()->create();

    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'employee-questions.csv';
    $import->file_path = 'imports/employee-questions.csv';
    $import->importer = EmployeeAdvisorQuestionImporter::class;
    $import->total_rows = 1;
    $import->save();

    $importer = app(EmployeeAdvisorQuestionImporter::class, [
        'import' => $import,
        'columnMap' => [
            'question' => 'question',
            'answer' => 'answer',
            'category' => 'category',
        ],
        'options' => [
            'employee_advisor_id' => $assistant->getKey(),
        ],
    ]);

    expect(fn () => $importer([
        'question' => 'What is the password reset process?',
        'answer' => 'Go to login and click forgot password.',
        'category' => 'NonexistentCategory',
    ]))->toThrow(RowImportFailedException::class);
});

it('validates required fields during employee advisor question import', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $assistant = AiAssistant::factory()->create();

    EmployeeAdvisorCategory::factory()->state([
        'employee_advisor_id' => $assistant->getKey(),
        'name' => 'Knowledge Base',
    ])->create();

    $import = new Import();
    $import->user()->associate($user);
    $import->file_name = 'employee-questions.csv';
    $import->file_path = 'imports/employee-questions.csv';
    $import->importer = EmployeeAdvisorQuestionImporter::class;
    $import->total_rows = 1;
    $import->save();

    $importer = app(EmployeeAdvisorQuestionImporter::class, [
        'import' => $import,
        'columnMap' => [
            'question' => 'question',
            'answer' => 'answer',
            'category' => 'category',
        ],
        'options' => [
            'employee_advisor_id' => $assistant->getKey(),
        ],
    ]);

    expect(fn () => $importer([
        'question' => null,
        'answer' => 'Go to login.',
        'category' => 'Knowledge Base',
    ]))->toThrow(ValidationException::class);

    expect(fn () => $importer([
        'question' => 'What is the password reset?',
        'answer' => null,
        'category' => 'Knowledge Base',
    ]))->toThrow(ValidationException::class);

    expect(fn () => $importer([
        'question' => 'What is the password reset?',
        'answer' => 'Go to login.',
        'category' => null,
    ]))->toThrow(RowImportFailedException::class);
});

it('shows import and export actions on employee questions page', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
    $user->givePermissionTo(['assistant_custom.view-any', 'assistant_custom.*.view', 'assistant_custom.create']);

    $assistant = AiAssistant::factory()->create();

    actingAs($user);

    livewire(ManageEmployeeAdvisorQuestions::class, ['record' => $assistant->getKey()])
        ->assertTableActionVisible(ExportAction::class)
        ->assertTableActionVisible(ImportAction::class);
});
