<?php

use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages\ManageQnaQuestions;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisorCategory;
use AdvisingApp\Ai\Models\QnaAdvisorQuestion;
use AdvisingApp\Ai\Tests\RequestFactories\QnaAdvisorQuestionRequestFactory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;

test('CreateQnAAdvisor Question is gated with proper access control', function () {

    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisor = QnaAdvisor::factory()->create();

    actingAs($user)
        ->get(
            QnaAdvisorResource::getUrl('manage-questions', [
                'record' => $qnaAdvisor,
            ])
        )
        ->assertForbidden();

    livewire(ManageQnaQuestions::class, ['record' => $qnaAdvisor->getKey()])
        ->assertForbidden();

    $user->givePermissionTo([
        'qna_advisor.view-any',
        'qna_advisor.*.view',
        'qna_advisor.create',
    ]);

    actingAs($user)
        ->get(
            QnaAdvisorResource::getUrl('manage-questions', [
                'record' => $qnaAdvisor,
            ])
        )->assertSuccessful();

    $qnaAdvisorQuestion = collect(QnaAdvisorQuestionRequestFactory::new()->create());

    livewire(ManageQnaQuestions::class, ['record' => $qnaAdvisor->getKey()])
        ->callTableAction('create', data: $qnaAdvisorQuestion->toArray())
        ->assertHasNoTableActionErrors();

    assertCount(1, QnaAdvisorQuestion::all());

    assertDatabaseHas(
        QnaAdvisorQuestion::class,
        $qnaAdvisorQuestion->toArray()
    );
});

test('CreateQnAAdvisor Question validates the inputs', function ($data, $errors) {

    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo([
        'qna_advisor.view-any',
        'qna_advisor.*.view',
        'qna_advisor.create',
    ]);

    $qnaAdvisor = QnaAdvisor::factory()->create();

    $qnaAdvisorQuestion = collect(QnaAdvisorQuestionRequestFactory::new($data)->create());

    actingAs($user);

    livewire(ManageQnaQuestions::class, ['record' => $qnaAdvisor->getKey()])
        ->callTableAction('create', data: $qnaAdvisorQuestion->toArray())
        ->assertHasTableActionErrors($errors);

    assertDatabaseMissing(
        QnaAdvisorQuestion::class,
        $qnaAdvisorQuestion->toArray()
    );
})->with(
    [
        'question required' => [
            QnaAdvisorQuestionRequestFactory::new()->state(['question' => null]),
            ['question' => 'required'],
        ],
        'question string' => [
            QnaAdvisorQuestionRequestFactory::new()->state(['question' => 1]),
            ['question' => 'string'],
        ],
        'question max' => [
            QnaAdvisorQuestionRequestFactory::new()->state(['question' => str()->random(257)]),
            ['question' => 'max'],
        ],
        'answer required' => [
            QnaAdvisorQuestionRequestFactory::new()->state(['answer' => null]),
            ['answer' => 'required'],
        ],
        'answer max' => [
            QnaAdvisorQuestionRequestFactory::new()->state(['answer' => str()->random(65537)]),
            ['answer' => 'max'],
        ],
    ]
);

test('EditQnAAdvisor Category is gated with proper access control', function () {

    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo([
        'qna_advisor.view-any',
        'qna_advisor.*.view',
        'qna_advisor.*.update',
    ]);

    $qnaAdvisor = QnaAdvisor::factory()->create();

    $qnaAdvisorQuestion = QnaAdvisorQuestion::factory()->state([
        'category_id' => QnaAdvisorCategory::factory()->state([
            'qna_advisor_id' => $qnaAdvisor->getKey(),
        ]),
    ])->create();

    $request = collect(QnaAdvisorQuestionRequestFactory::new()->create());

    actingAs($user);

    livewire(ManageQnaQuestions::class, ['record' => $qnaAdvisor->getKey()])
        ->callTableAction('edit', record: $qnaAdvisorQuestion->getKey(), data: $request->toArray())
        ->assertHasNoTableActionErrors();

    assertDatabaseHas(
        QnaAdvisorQuestion::class,
        $request->toArray()
    );
});

test('EditQnAAdvisor Category validates the inputs', function ($data, $errors) {

    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo([
        'qna_advisor.view-any',
        'qna_advisor.*.view',
        'qna_advisor.*.update',
    ]);

    $qnaAdvisor = QnaAdvisor::factory()->create();

    $qnaAdvisorQuestion = QnaAdvisorQuestion::factory()->state([
        'category_id' => QnaAdvisorCategory::factory()->state([
            'qna_advisor_id' => $qnaAdvisor->getKey(),
        ]),
    ])->create();

    $request = QnaAdvisorQuestionRequestFactory::new($data)->create();

    actingAs($user);

    livewire(ManageQnaQuestions::class, ['record' => $qnaAdvisor->getKey()])
        ->callTableAction('edit', record: $qnaAdvisorQuestion->getKey(), data: $request)
        ->assertHasTableActionErrors($errors);
})
->with(
    [
        'question required' => [
            QnaAdvisorQuestionRequestFactory::new()->state(['question' => null]),
            ['question' => 'required'],
        ],
        'question string' => [
            QnaAdvisorQuestionRequestFactory::new()->state(['question' => 1]),
            ['question' => 'string'],
        ],
        'question max' => [
            QnaAdvisorQuestionRequestFactory::new()->state(['question' => str()->random(257)]),
            ['question' => 'max'],
        ],
        'answer required' => [
            QnaAdvisorQuestionRequestFactory::new()->state(['answer' => null]),
            ['answer' => 'required'],
        ],
        'answer max' => [
            QnaAdvisorQuestionRequestFactory::new()->state(['answer' => str()->random(65537)]),
            ['answer' => 'max'],
        ],
    ]
);
