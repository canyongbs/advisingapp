<?php

use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages\ManageCategories;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisorCategory;
use AdvisingApp\Ai\Tests\RequestFactories\QnaAdvisorCategoryRequestFactory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;

test('CreateQnaAdvisor Category is gated with proper access control', function () {
    
    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisor = QnaAdvisor::factory()->create();

    actingAs($user)
        ->get(
            QnaAdvisorResource::getUrl('manage-categories', [
                'record' => $qnaAdvisor,
            ])
        )
        ->assertForbidden();

    livewire(ManageCategories::class, ['record' => $qnaAdvisor->getKey()])
        ->assertForbidden();

    $user->givePermissionTo(['qna_advisor.view-any', 'qna_advisor.*.view', 'qna_advisor.create']);

    actingAs($user)
        ->get(
            QnaAdvisorResource::getUrl('manage-categories', [
                'record' => $qnaAdvisor,
            ])
        )->assertSuccessful();

    $qnaAdvisorCategory = collect(QnaAdvisorCategoryRequestFactory::new()->create());

    livewire(ManageCategories::class, ['record' => $qnaAdvisor->getKey()])
        ->callTableAction('create', data: $qnaAdvisorCategory->toArray())
        ->assertHasNoTableActionErrors();

    assertCount(1, QnaAdvisorCategory::all());

    assertDatabaseHas(
        QnaAdvisorCategory::class,
        $qnaAdvisorCategory->toArray()
    );
});

test('CreateQnaAdvisor Category validates the inputs', function ($data, $errors) {

    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisor = QnaAdvisor::factory()->create();

    actingAs($user)
        ->get(
            QnaAdvisorResource::getUrl('manage-categories', [
                'record' => $qnaAdvisor,
            ])
        )
        ->assertForbidden();

    livewire(ManageCategories::class, ['record' => $qnaAdvisor->getKey()])
        ->assertForbidden();

    $user->givePermissionTo(['qna_advisor.view-any', 'qna_advisor.*.view', 'qna_advisor.create']);

    actingAs($user)
        ->get(
            QnaAdvisorResource::getUrl('manage-categories', [
                'record' => $qnaAdvisor,
            ])
        )->assertSuccessful();

    $qnaAdvisorCategory = collect(QnaAdvisorCategoryRequestFactory::new($data)->create());

    livewire(ManageCategories::class, ['record' => $qnaAdvisor->getKey()])
        ->callTableAction('create', data: $qnaAdvisorCategory->toArray())
        ->assertHasTableActionErrors($errors);

    assertDatabaseMissing(
        QnaAdvisorCategory::class,
        $qnaAdvisorCategory->toArray()
    );
})->with(
    [
        'name required' => [
            QnaAdvisorCategoryRequestFactory::new()->without('name'),
            ['name' => 'required'],
        ],
        'name string' => [
            QnaAdvisorCategoryRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            QnaAdvisorCategoryRequestFactory::new()->state(['name' => str()->random(257)]),
            ['name' => 'max'],
        ],
        'description required' => [
            QnaAdvisorCategoryRequestFactory::new()->without('description'),
            ['description' => 'required'],
        ],
        'description max' => [
            QnaAdvisorCategoryRequestFactory::new()->state(['description' => str()->random(65537)]),
            ['description' => 'max'],
        ],
    ]
);

test('EditQnaAdvisor Category is gated with proper access control', function () {

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
    $qnaAdvisorCategory = QnaAdvisorCategory::factory()->state([
        'qna_advisor_id' => $qnaAdvisor->getKey(),
    ])->create();

    $request = collect(QnaAdvisorCategoryRequestFactory::new()->create());

    actingAs($user);

    livewire(ManageCategories::class, ['record' => $qnaAdvisor->getKey()])
        ->callTableAction('edit', record: $qnaAdvisorCategory->getKey(), data: $request->toArray())
        ->assertHasNoTableActionErrors();

    assertDatabaseHas(
        QnaAdvisorCategory::class,
        $request->toArray()
    );
});

test('EditQnaAdvisor Category validates the inputs', function ($data, $errors) {

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

    QnaAdvisorCategory::factory()->state([
        'name' => 'Education',
        'qna_advisor_id' => $qnaAdvisor->getKey(),
    ])->create();

    $qnaAdvisorCategory = QnaAdvisorCategory::factory()->state([
        'qna_advisor_id' => $qnaAdvisor->getKey(),
    ])->create();

    $request = QnaAdvisorCategoryRequestFactory::new($data)->create();

    actingAs($user);

    livewire(ManageCategories::class, ['record' => $qnaAdvisor->getKey()])
        ->callTableAction('edit', record: $qnaAdvisorCategory->getKey(), data: $request)
        ->assertHasTableActionErrors($errors);
})
    ->with(
        [
            'name required' => [
                QnaAdvisorCategoryRequestFactory::new()->state(['name' => null]),
                ['name' => 'required'],
            ],
            'name string' => [
                QnaAdvisorCategoryRequestFactory::new()->state(['name' => 1]),
                ['name' => 'string'],
            ],
            'name unique' => [
                QnaAdvisorCategoryRequestFactory::new()->state(['name' => 'Education']),
                ['name' => 'unique'],
            ],
            'name max' => [
                QnaAdvisorCategoryRequestFactory::new()->state(['name' => str()->random(257)]),
                ['name' => 'max'],
            ],
            'description required' => [
                QnaAdvisorCategoryRequestFactory::new()->state(['description' => null]),
                ['description' => 'required'],
            ],
            'description max' => [
                QnaAdvisorCategoryRequestFactory::new()->state(['description' => str()->random(65537)]),
                ['description' => 'max'],
            ],
        ]
    );
