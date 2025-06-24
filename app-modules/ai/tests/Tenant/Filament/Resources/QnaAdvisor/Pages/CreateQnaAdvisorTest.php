<?php

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages\CreateQnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Tests\RequestFactories\QnaAdvisorRequestFactory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Enum;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

use function Tests\asSuperAdmin;

test('CreateQnaAdvisor is gated with proper access control', function () {
    Storage::fake('s3');

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    assertDatabaseCount(QnaAdvisor::class, 0);

    actingAs($user)
        ->get(
            QnaAdvisorResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateQnaAdvisor::class)
        ->assertForbidden();

    $user->givePermissionTo(['qna_advisor.view-any', 'qna_advisor.create']);

    actingAs($user)
        ->get(
            QnaAdvisorResource::getUrl('create')
        )->assertSuccessful();

    $qnaAdvisor = collect(QnaAdvisorRequestFactory::new()->create());

    livewire(CreateQnaAdvisor::class)
        ->fillForm($qnaAdvisor->except(['model'])->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, QnaAdvisor::all());

    assertDatabaseHas(
        QnaAdvisor::class,
        $qnaAdvisor->except([
            'avatar',
            'model',
            'instructions',
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

test('CreateQnAAdvisor validates the inputs', function ($data, $errors) {
    Storage::fake('s3');

    asSuperAdmin();

    $request = collect(QnaAdvisorRequestFactory::new($data)->create());

    livewire(CreateQnaAdvisor::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertDatabaseMissing(
        QnaAdvisor::class,
        $request->except([
            'avatar',
        ])->toArray()
    );
})->with(
    [
        'name required' => [
            QnaAdvisorRequestFactory::new()->without('name'),
            ['name' => 'required'],
        ],
        'name string' => [
            QnaAdvisorRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            QnaAdvisorRequestFactory::new()->state(['name' => str()->random(257)]),
            ['name' => 'max'],
        ],
        'description required' => [
            QnaAdvisorRequestFactory::new()->without('description'),
            ['description' => 'required'],
        ],
        'description max' => [
            QnaAdvisorRequestFactory::new()->state(['description' => str()->random(65537)]),
            ['description' => 'max'],
        ],
        'instructions required' => [
            QnaAdvisorRequestFactory::new()->state(['instructions' => null]),
            ['instructions' => 'required'],
        ],
        'model required' => [
            QnaAdvisorRequestFactory::new()->state(['model' => null]),
            ['model' => 'required'],
        ],
        'model must be correct enum' => [
            QnaAdvisorRequestFactory::new()->state(['model' => AiModel::OpenAiGpt4]),
            ['model' => Enum::class],
        ],
    ]
);
