<?php

use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\ManageQnAQuestions;
use AdvisingApp\Ai\Models\QnAAdvisor;
use AdvisingApp\Ai\Models\QnAAdvisorCategory;
use AdvisingApp\Ai\Models\QnAAdvisorQuestion;
use AdvisingApp\Ai\Tests\RequestFactories\QnAAdvisorQuestionRequestFactory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;

test('CreateQnAAdvisor Question is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo(['qna_advisor.view-any', 'qna_advisor.*.view', 'qna_advisor.create']);

    $qnaAdvisor = QnAAdvisor::factory()->create();

    actingAs($user)
        ->get(
            QnAAdvisorResource::getUrl('manage-questions', [
                'record' => $qnaAdvisor,
            ])
        )
        ->assertForbidden();

    livewire(ManageQnAQuestions::class, ['record' => $qnaAdvisor->getKey()])
        ->assertForbidden();

    $user->givePermissionTo([
        'qna_advisor.view-any',
        'qna_advisor.*.view',
        'qna_advisor_category.view-any',
        'qna_advisor_category.*.view',
        'qna_advisor_question.view-any',
        'qna_advisor_question.*.view',
        'qna_advisor_question.create',
    ]);

    actingAs($user)
        ->get(
            QnAAdvisorResource::getUrl('manage-questions', [
                'record' => $qnaAdvisor,
            ])
        )->assertSuccessful();

    $qnaAdvisorQuestion = collect(QnAAdvisorQuestionRequestFactory::new()->create());

    livewire(ManageQnAQuestions::class, ['record' => $qnaAdvisor->getKey()])
        ->callTableAction('create', data: $qnaAdvisorQuestion->toArray())
        ->assertHasNoTableActionErrors();

    assertCount(1, QnAAdvisorQuestion::all());

    assertDatabaseHas(
        QnAAdvisorQuestion::class,
        $qnaAdvisorQuestion->toArray()
    );
});

test('CreateQnAAdvisor Question validates the inputs', function ($data, $errors) {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo([
        'qna_advisor.view-any',
        'qna_advisor.*.view',
        'qna_advisor_category.view-any',
        'qna_advisor_category.*.view',
        'qna_advisor_question.view-any',
        'qna_advisor_question.*.view',
        'qna_advisor_question.create',
    ]);

    $qnaAdvisor = QnAAdvisor::factory()->create();

    $qnaAdvisorQuestion = collect(QnAAdvisorQuestionRequestFactory::new($data)->create());

    actingAs($user);

    livewire(ManageQnAQuestions::class, ['record' => $qnaAdvisor->getKey()])
        ->callTableAction('create', data: $qnaAdvisorQuestion->toArray())
        ->assertHasTableActionErrors($errors);

    assertDatabaseMissing(
        QnAAdvisorQuestion::class,
        $qnaAdvisorQuestion->toArray()
    );
})->with(
    [
        'question required' => [
            QnAAdvisorQuestionRequestFactory::new()->state(['question' => null]),
            ['question' => 'required'],
        ],
        'question string' => [
            QnAAdvisorQuestionRequestFactory::new()->state(['question' => 1]),
            ['question' => 'string'],
        ],
        'question max' => [
            QnAAdvisorQuestionRequestFactory::new()->state(['question' => str()->random(257)]),
            ['question' => 'max'],
        ],
        'answer required' => [
            QnAAdvisorQuestionRequestFactory::new()->state(['answer' => null]),
            ['answer' => 'required'],
        ],
        'answer max' => [
            QnAAdvisorQuestionRequestFactory::new()->state(['answer' => str()->random(65537)]),
            ['answer' => 'max'],
        ],
    ]
);

test('EditQnAAdvisor Category is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo([
        'qna_advisor.view-any',
        'qna_advisor.*.view',
        'qna_advisor_category.view-any',
        'qna_advisor_category.*.view',
        'qna_advisor_question.view-any',
        'qna_advisor_question.*.view',
        'qna_advisor_question.*.update',
    ]);

    $qnaAdvisor = QnAAdvisor::factory()->create();

    $qnaAdvisorQuestion = QnAAdvisorQuestion::factory()->state([
        'category_id' => QnAAdvisorCategory::factory()->state([
            'qn_a_advisor_id' => $qnaAdvisor->getKey(),
        ]),
    ])->create();

    $request = collect(QnAAdvisorQuestionRequestFactory::new()->create());

    actingAs($user);

    livewire(ManageQnAQuestions::class, ['record' => $qnaAdvisor->getKey()])
        ->callTableAction('edit', record: $qnaAdvisorQuestion->getKey(), data: $request->toArray())
        ->assertHasNoTableActionErrors();

    assertDatabaseHas(
        QnAAdvisorQuestion::class,
        $request->toArray()
    );
});

test('EditQnAAdvisor Category validates the inputs', function ($data, $errors) {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo([
        'qna_advisor.view-any',
        'qna_advisor.*.view',
        'qna_advisor_category.view-any',
        'qna_advisor_category.*.view',
        'qna_advisor_question.view-any',
        'qna_advisor_question.*.view',
        'qna_advisor_question.*.update',
    ]);

    $qnaAdvisor = QnAAdvisor::factory()->create();

    $qnaAdvisorQuestion = QnAAdvisorQuestion::factory()->state([
        'category_id' => QnAAdvisorCategory::factory()->state([
            'qn_a_advisor_id' => $qnaAdvisor->getKey(),
        ]),
    ])->create();

    $request = QnAAdvisorQuestionRequestFactory::new($data)->create();

    actingAs($user);

    livewire(ManageQnAQuestions::class, ['record' => $qnaAdvisor->getKey()])
        ->callTableAction('edit', record: $qnaAdvisorQuestion->getKey(), data: $request)
        ->assertHasTableActionErrors($errors);
})
    ->with(
        [
            'question required' => [
                QnAAdvisorQuestionRequestFactory::new()->state(['question' => null]),
                ['question' => 'required'],
            ],
            'question string' => [
                QnAAdvisorQuestionRequestFactory::new()->state(['question' => 1]),
                ['question' => 'string'],
            ],
            'question max' => [
                QnAAdvisorQuestionRequestFactory::new()->state(['question' => str()->random(257)]),
                ['question' => 'max'],
            ],
            'answer required' => [
                QnAAdvisorQuestionRequestFactory::new()->state(['answer' => null]),
                ['answer' => 'required'],
            ],
            'answer max' => [
                QnAAdvisorQuestionRequestFactory::new()->state(['answer' => str()->random(65537)]),
                ['answer' => 'max'],
            ],
        ]
    );
