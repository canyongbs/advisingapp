<?php

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\CreateQnAAdvisor;
use AdvisingApp\Ai\Models\QnAAdvisor;
use AdvisingApp\Ai\Settings\AiQnAAdvisorSettings;
use AdvisingApp\Ai\Tests\RequestFactories\QnAAdvisorRequestFactory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Enum;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Tests\asSuperAdmin;

test('CreateQnAAdvisor is gated with proper access control', function () {
    Storage::fake('s3');

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnAAdvisorSetting = app(AiQnAAdvisorSettings::class);
    $qnAAdvisorSetting->allow_selection_of_model = true;
    $qnAAdvisorSetting->save();

    assertDatabaseCount(QnAAdvisor::class, 0);

    actingAs($user)
        ->get(
            QnAAdvisorResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateQnAAdvisor::class)
        ->assertForbidden();

    $user->givePermissionTo('qna_advisor.view-any');
    $user->givePermissionTo('qna_advisor.create');

    actingAs($user)
        ->get(
            QnAAdvisorResource::getUrl('create')
        )->assertSuccessful();

    $qnaAdvisor = collect(QnAAdvisorRequestFactory::new()->create());

    livewire(CreateQnAAdvisor::class)
        ->fillForm($qnaAdvisor->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, QnAAdvisor::all());

    assertDatabaseHas(
        QnAAdvisor::class,
        $qnaAdvisor->except([
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

test('CreateQnAAdvisor validates the inputs', function ($data, $errors) {
    
    Storage::fake('s3');

    asSuperAdmin();

    $request = collect(QnAAdvisorRequestFactory::new($data)->create());

    livewire(CreateQnAAdvisor::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertDatabaseMissing(
        QnAAdvisor::class,
        $request->except([
            'avatar',
        ])->toArray()
    );
})->with(
    [
        'name required' => [
            QnAAdvisorRequestFactory::new()->without('name'),
            ['name' => 'required'],
        ],
        'name string' => [
            QnAAdvisorRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            QnAAdvisorRequestFactory::new()->state(['name' => str()->random(257)]),
            ['name' => 'max'],
        ],
        'description required' => [
            QnAAdvisorRequestFactory::new()->without('description'),
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
