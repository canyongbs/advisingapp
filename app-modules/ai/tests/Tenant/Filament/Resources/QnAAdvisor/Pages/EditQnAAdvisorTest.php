<?php

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\EditQnAAdvisor;
use AdvisingApp\Ai\Models\QnAAdvisor;
use AdvisingApp\Ai\Tests\RequestFactories\QnAAdvisorRequestFactory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Enum;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

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

    $user->givePermissionTo('qna_advisor.view-any');
    $user->givePermissionTo('qna_advisor.*.update');

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

test('EditIncident validates the inputs', function ($data, $errors) {
    Storage::fake('s3');

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    actingAs($user);

    $user->givePermissionTo('qna_advisor.view-any');
    $user->givePermissionTo('qna_advisor.*.update');

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

