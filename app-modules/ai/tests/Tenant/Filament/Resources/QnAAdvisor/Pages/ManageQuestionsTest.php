<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
