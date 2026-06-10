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
use AdvisingApp\Ai\Filament\Resources\AiAssistants\Pages\ManageEmployeeAdvisorQuestions;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\EmployeeAdvisorCategory;
use AdvisingApp\Ai\Models\EmployeeAdvisorQuestion;
use AdvisingApp\Ai\Tests\RequestFactories\EmployeeAdvisorQuestionRequestFactory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;
use App\Settings\LicenseSettings;

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

    livewire(ManageEmployeeAdvisorQuestions::class, ['record' => $assistant->getKey()])
        ->callTableAction('create', data: $questionData->toArray())
        ->assertHasNoTableActionErrors();

    assertCount(1, EmployeeAdvisorQuestion::all());

    assertDatabaseHas(
        EmployeeAdvisorQuestion::class,
        $questionData->toArray()
    );
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

    livewire(ManageEmployeeAdvisorQuestions::class, ['record' => $assistant->getKey()])
        ->callTableAction('create', data: $questionData->toArray())
        ->assertHasTableActionErrors($errors);

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
