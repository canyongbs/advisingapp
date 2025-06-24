<?php

use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages\EditQnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Tests\RequestFactories\QnaAdvisorRequestFactory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

test('EditQnaAdvisor is gated with proper access control', function () {
    Storage::fake('s3');

    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisor = QnaAdvisor::factory()->create();

    actingAs($user)
        ->get(
            QnaAdvisorResource::getUrl('edit', [
                'record' => $qnaAdvisor,
            ])
        )->assertForbidden();

    livewire(EditQnaAdvisor::class, [
        'record' => $qnaAdvisor->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo(['qna_advisor.view-any', 'qna_advisor.*.view', 'qna_advisor.*.update']);

    actingAs($user)
        ->get(
            QnaAdvisorResource::getUrl('edit', [
                'record' => $qnaAdvisor,
            ])
        )->assertSuccessful();

    $request = collect(QnaAdvisorRequestFactory::new()->create());

    livewire(EditQnaAdvisor::class, [
        'record' => $qnaAdvisor->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(
        QnaAdvisor::class,
        $request->except([
            'avatar',
            'instructions',
            'model',
        ])->toArray()
    );

    assertDatabaseHas(
        Media::class,
        [
            'model_type' => (new (QnaAdvisor::class))->getMorphClass(),
            'model_id' => QnaAdvisor::query()->first()->getKey(),
            'collection_name' => 'avatar',
        ]
    );
});

test('EditQnaAdvisor validates the inputs', function ($data, $errors) {
    Storage::fake('s3');

    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    actingAs($user);

    $user->givePermissionTo(['qna_advisor.view-any', 'qna_advisor.*.view', 'qna_advisor.*.update']);

    $qnaAdvisor = QnaAdvisor::factory()->create();

    $request = QnaAdvisorRequestFactory::new($data)->create();

    livewire(EditQnaAdvisor::class, [
        'record' => $qnaAdvisor->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors($errors);
})->with(
    [
        'name required' => [
            QnaAdvisorRequestFactory::new()->state(['name' => null]),
            ['name' => 'required'],
        ],
        'name string' => [
            QnaAdvisorRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            QnaAdvisorRequestFactory::new()->state(['name' => str()->random(258)]),
            ['name' => 'max'],
        ],
        'description required' => [
            QnaAdvisorRequestFactory::new()->state(['description' => null]),
            ['description' => 'required'],
        ],
        'description max' => [
            QnaAdvisorRequestFactory::new()->state(['description' => str()->random(65537)]),
            ['description' => 'max'],
        ],
    ]
);

test('archive action visible when QnA Advisor is not archived', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisor = QnaAdvisor::factory()->create();

    $user->givePermissionTo('qna_advisor.view-any');
    $user->givePermissionTo('qna_advisor.*.view');
    $user->givePermissionTo('qna_advisor.*.update');

    actingAs($user);

    livewire(EditQnaAdvisor::class, [
        'record' => $qnaAdvisor->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionVisible('archive')
        ->assertActionHidden('restore');
});

test('restore action visible when QnA Advisor is archived', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisor = QnaAdvisor::factory()->state([
        'archived_at' => now(),
    ])->create();

    $user->givePermissionTo('qna_advisor.view-any');
    $user->givePermissionTo('qna_advisor.*.view');
    $user->givePermissionTo('qna_advisor.*.update');

    actingAs($user);

    livewire(EditQnaAdvisor::class, [
        'record' => $qnaAdvisor->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionVisible('restore')
        ->assertActionHidden('archive');
});
