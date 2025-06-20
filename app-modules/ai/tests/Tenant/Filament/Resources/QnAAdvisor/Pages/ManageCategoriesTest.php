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
