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

test('Create QnA Advisor Question is gated with proper access control', function () {
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
});

test('can create QnA Advisor Question', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisor = QnaAdvisor::factory()->create();

    $user->givePermissionTo([
        'qna_advisor.view-any',
        'qna_advisor.*.view',
        'qna_advisor.create',
    ]);

    $qnaAdvisorQuestion = collect(QnaAdvisorQuestionRequestFactory::new()->create());

    actingAs($user);

    livewire(ManageQnaQuestions::class, ['record' => $qnaAdvisor->getKey()])
        ->callTableAction('create', data: $qnaAdvisorQuestion->toArray())
        ->assertHasNoTableActionErrors();

    assertCount(1, QnaAdvisorQuestion::all());

    assertDatabaseHas(
        QnaAdvisorQuestion::class,
        $qnaAdvisorQuestion->toArray()
    );
});

test('Create QnA Advisor Question validates the inputs', function ($data, $errors) {
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
        'category_id required' => [
            QnaAdvisorQuestionRequestFactory::new()->state(['category_id' => null]),
            ['category_id' => 'required'],
        ],
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

test('can edit QnA Advisor Question', function () {
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

test('Edit QnA Advisor Question validates the inputs', function ($data, $errors) {
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
            'category_id required' => [
                QnaAdvisorQuestionRequestFactory::new()->state(['category_id' => null]),
                ['category_id' => 'required'],
            ],
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
