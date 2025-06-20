<?php

use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\ManageCategories;
use AdvisingApp\Ai\Models\QnAAdvisor;
use AdvisingApp\Ai\Models\QnAAdvisorCategory;
use AdvisingApp\Ai\Tests\RequestFactories\QnAAdvisorCategoryRequestFactory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;

test('CreateQnAAdvisor Category is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo(['qna_advisor.view-any', 'qna_advisor.*.view']);

    $qnaAdvisor = QnAAdvisor::factory()->create();

    actingAs($user)
        ->get(
            QnAAdvisorResource::getUrl('manage-categories', [
                'record' => $qnaAdvisor,
            ])
        )
        ->assertForbidden();

    livewire(ManageCategories::class, ['record' => $qnaAdvisor->getKey()])
        ->assertForbidden();

    $user->givePermissionTo(['qna_advisor_category.view-any', 'qna_advisor_category.*.view', 'qna_advisor_category.create']);

    actingAs($user)
        ->get(
            QnAAdvisorResource::getUrl('manage-categories', [
                'record' => $qnaAdvisor,
            ])
        )->assertSuccessful();

    $qnaAdvisorCategory = collect(QnAAdvisorCategoryRequestFactory::new()->create());

    livewire(ManageCategories::class, ['record' => $qnaAdvisor->getKey()])
        ->callTableAction('create', data: $qnaAdvisorCategory->toArray())
        ->assertHasNoTableActionErrors();

    assertCount(1, QnAAdvisorCategory::all());

    assertDatabaseHas(
        QnAAdvisorCategory::class,
        $qnaAdvisorCategory->toArray()
    );
});

test('CreateQnAAdvisor Category validates the inputs', function ($data, $errors) {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo(['qna_advisor.view-any', 'qna_advisor.*.view']);

    $qnaAdvisor = QnAAdvisor::factory()->create();

    actingAs($user)
        ->get(
            QnAAdvisorResource::getUrl('manage-categories', [
                'record' => $qnaAdvisor,
            ])
        )
        ->assertForbidden();

    livewire(ManageCategories::class, ['record' => $qnaAdvisor->getKey()])
        ->assertForbidden();

    $user->givePermissionTo(['qna_advisor_category.view-any', 'qna_advisor_category.*.view', 'qna_advisor_category.create']);

    actingAs($user)
        ->get(
            QnAAdvisorResource::getUrl('manage-categories', [
                'record' => $qnaAdvisor,
            ])
        )->assertSuccessful();

    $qnaAdvisorCategory = collect(QnAAdvisorCategoryRequestFactory::new($data)->create());

    livewire(ManageCategories::class, ['record' => $qnaAdvisor->getKey()])
        ->callTableAction('create', data: $qnaAdvisorCategory->toArray())
        ->assertHasTableActionErrors($errors);

    assertDatabaseMissing(
        QnAAdvisorCategory::class,
        $qnaAdvisorCategory->toArray()
    );
})->with(
    [
        'name required' => [
            QnAAdvisorCategoryRequestFactory::new()->without('name'),
            ['name' => 'required'],
        ],
        'name string' => [
            QnAAdvisorCategoryRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            QnAAdvisorCategoryRequestFactory::new()->state(['name' => str()->random(257)]),
            ['name' => 'max'],
        ],
        'description required' => [
            QnAAdvisorCategoryRequestFactory::new()->without('description'),
            ['description' => 'required'],
        ],
        'description max' => [
            QnAAdvisorCategoryRequestFactory::new()->state(['description' => str()->random(65537)]),
            ['description' => 'max'],
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
        'qna_advisor_category.*.update',
    ]);

    $qnaAdvisor = QnAAdvisor::factory()->create();
    $qnaAdvisorCategory = QnAAdvisorCategory::factory()->state([
        'qn_a_advisor_id' => $qnaAdvisor->getKey(),
    ])->create();

    $request = collect(QnAAdvisorCategoryRequestFactory::new()->create());

    actingAs($user);

    livewire(ManageCategories::class, ['record' => $qnaAdvisor->getKey()])
        ->callTableAction('edit', record: $qnaAdvisorCategory->getKey(), data: $request->toArray())
        ->assertHasNoTableActionErrors();

    assertDatabaseHas(
        QnAAdvisorCategory::class,
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
        'qna_advisor_category.*.update',
    ]);

    $qnaAdvisor = QnAAdvisor::factory()->create();

    QnAAdvisorCategory::factory()->state([
        'name' => 'Education',
        'qn_a_advisor_id' => $qnaAdvisor->getKey(),
    ])->create();

    $qnaAdvisorCategory = QnAAdvisorCategory::factory()->state([
        'qn_a_advisor_id' => $qnaAdvisor->getKey(),
    ])->create();

    $request = QnAAdvisorCategoryRequestFactory::new($data)->create();

    actingAs($user);

    livewire(ManageCategories::class, ['record' => $qnaAdvisor->getKey()])
        ->callTableAction('edit', record: $qnaAdvisorCategory->getKey(), data: $request)
        ->assertHasTableActionErrors($errors);
})
    ->with(
        [
            'name required' => [
                QnAAdvisorCategoryRequestFactory::new()->state(['name' => null]),
                ['name' => 'required'],
            ],
            'name string' => [
                QnAAdvisorCategoryRequestFactory::new()->state(['name' => 1]),
                ['name' => 'string'],
            ],
            'name unique' => [
                QnAAdvisorCategoryRequestFactory::new()->state(['name' => 'Education']),
                ['name' => 'unique'],
            ],
            'name max' => [
                QnAAdvisorCategoryRequestFactory::new()->state(['name' => str()->random(257)]),
                ['name' => 'max'],
            ],
            'description required' => [
                QnAAdvisorCategoryRequestFactory::new()->state(['description' => null]),
                ['description' => 'required'],
            ],
            'description max' => [
                QnAAdvisorCategoryRequestFactory::new()->state(['description' => str()->random(65537)]),
                ['description' => 'max'],
            ],
        ]
    );
