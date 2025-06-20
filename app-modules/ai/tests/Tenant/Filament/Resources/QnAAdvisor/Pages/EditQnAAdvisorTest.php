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

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\EditQnAAdvisor;
use AdvisingApp\Ai\Models\QnAAdvisor;
use AdvisingApp\Ai\Tests\RequestFactories\QnAAdvisorRequestFactory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Enum;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

test('EditQnAAdvisor is gated with proper access control', function () {
    Storage::fake('s3');

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisor = QnAAdvisor::factory()->create();

    actingAs($user)
        ->get(
            QnAAdvisorResource::getUrl('edit', [
                'record' => $qnaAdvisor,
            ])
        )->assertForbidden();

    livewire(EditQnAAdvisor::class, [
        'record' => $qnaAdvisor->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo(['qna_advisor.view-any', 'qna_advisor.*.update']);

    actingAs($user)
        ->get(
            QnAAdvisorResource::getUrl('edit', [
                'record' => $qnaAdvisor,
            ])
        )->assertSuccessful();

    $request = collect(QnAAdvisorRequestFactory::new()->create());

    livewire(EditQnAAdvisor::class, [
        'record' => $qnaAdvisor->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(
        QnAAdvisor::class,
        $request->except([
            'avatar',
        ])->toArray()
    );

    assertDatabaseHas(
        Media::class,
        [
            'model_type' => (new (QnAAdvisor::class))->getMorphClass(),
            'model_id' => QnAAdvisor::query()->first()->getKey(),
            'collection_name' => 'avatar',
        ]
    );
});

test('EditQnAAdvisor validates the inputs', function ($data, $errors) {
    Storage::fake('s3');

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    actingAs($user);

    $user->givePermissionTo(['qna_advisor.view-any', 'qna_advisor.*.update']);

    $qnaAdvisor = QnAAdvisor::factory()->create();

    $request = QnAAdvisorRequestFactory::new($data)->create();

    livewire(EditQnAAdvisor::class, [
        'record' => $qnaAdvisor->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors($errors);
})->with(
    [
        'name required' => [
            QnAAdvisorRequestFactory::new()->state(['name' => null]),
            ['name' => 'required'],
        ],
        'name string' => [
            QnAAdvisorRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            QnAAdvisorRequestFactory::new()->state(['name' => str()->random(258)]),
            ['name' => 'max'],
        ],
        'description required' => [
            QnAAdvisorRequestFactory::new()->state(['description' => null]),
            ['description' => 'required'],
        ],
        'description max' => [
            QnAAdvisorRequestFactory::new()->state(['description' => str()->random(65537)]),
            ['description' => 'max'],
        ],
        'model required' => [
            QnAAdvisorRequestFactory::new()->state(['model' => null]),
            ['model' => 'required'],
        ],
        'model must be correct enum' => [
            QnAAdvisorRequestFactory::new()->state(['model' => AiModel::OpenAiGpt4]),
            ['model' => Enum::class],
        ],
    ]
);

test('archive action visible when QnA Advisor is not archived', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisors = QnAAdvisor::factory()->create();

    $user->givePermissionTo('qna_advisor.view-any');
    $user->givePermissionTo('qna_advisor.*.view');
    $user->givePermissionTo('qna_advisor.*.update');

    actingAs($user);

    livewire(EditQnAAdvisor::class, [
        'record' => $qnaAdvisors->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionVisible('archive')
        ->assertActionHidden('restore');
});

test('restore action visible when QnA Advisor is archived', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisors = QnAAdvisor::factory()->state([
        'archived_at' => now(),
    ])->create();

    $user->givePermissionTo('qna_advisor.view-any');
    $user->givePermissionTo('qna_advisor.*.view');
    $user->givePermissionTo('qna_advisor.*.update');

    actingAs($user);

    livewire(EditQnAAdvisor::class, [
        'record' => $qnaAdvisors->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionVisible('restore')
        ->assertActionHidden('archive');
});
