<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Ai\Filament\Resources\AiAssistantResource\Pages\EditAiAssistant;

use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Tests\Feature\Filament\Resources\AiAssistantResource\RequestFactories\CreateAiAssistantRequestFactory;

use AdvisingApp\Authorization\Enums\LicenseType;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Enum;
use Spatie\MediaLibrary\MediaCollections\Models\Media;



/** @var array<LicenseType> $licenses */
$licenses = [
    LicenseType::ConversationalAi,
];

$permissions = [
    'ai_assistant.view-any',
    'ai_assistant.*.update',
];

it('cannot render without a license', function () use ($permissions) {
    actingAs(user(
        permissions: $permissions
    ));

    $aiAssistant = AiAssistant::factory()->create();

    get(EditAiAssistant::getUrl([
        'record' => $aiAssistant->getRouteKey(),
    ]))
        ->assertForbidden();
});

it('cannot render without permissions', function () use ($licenses) {
    actingAs(user(
        licenses: $licenses
    ));

    $aiAssistant = AiAssistant::factory()->create();

    get(EditAiAssistant::getUrl([
        'record' => $aiAssistant->getRouteKey(),
    ]))
        ->assertForbidden();
});

it('cannot render without proper features enabled', function () use ($licenses, $permissions) {
    actingAs(user(
        licenses: $licenses,
        permissions: $permissions
    ));

    $aiAssistant = AiAssistant::factory()->create();

    get(EditAiAssistant::getUrl([
        'record' => $aiAssistant->getRouteKey(),
    ]))
        ->assertForbidden();
});

it('can render', function () use ($licenses, $permissions) {
    actingAs(user(
        licenses: $licenses,
        permissions: $permissions
    ));

    $settings = app(LicenseSettings::class);

    $settings->data->addons->customAiAssistants = true;

    $settings->save();

    $aiAssistant = AiAssistant::factory()->create();

    get(EditAiAssistant::getUrl([
        'record' => $aiAssistant->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('can edit a record', function () use ($licenses, $permissions) {
    Storage::fake('s3');

    actingAs(user(
        licenses: $licenses,
        permissions: $permissions
    ));

    $settings = app(LicenseSettings::class);

    $settings->data->addons->customAiAssistants = true;

    $settings->save();

    $aiAssistant = AiAssistant::factory()->create();

    $request = collect(CreateAiAssistantRequestFactory::new()->create());

    livewire(EditAiAssistant::class, [
        'record' => $aiAssistant->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertHasNoErrors();

    assertDatabaseHas(
        AiAssistant::class,
        $request->except([
            'avatar',
        ])->toArray()
    );

    assertDatabaseHas(
        Media::class,
        [
            'model_type' => (new (AiAssistant::class))->getMorphClass(),
            'model_id' => AiAssistant::query()->first()->getKey(),
            'collection_name' => 'avatar',
        ]
    );
});

it('validates the inputs', function ($data, $errors) use ($licenses, $permissions) {
    Storage::fake('s3');

    actingAs(user(
        licenses: $licenses,
        permissions: $permissions
    ));

    $settings = app(LicenseSettings::class);

    $settings->data->addons->customAiAssistants = true;

    $settings->save();

    $request = collect(CreateAiAssistantRequestFactory::new($data)->create());

    $aiAssistant = AiAssistant::factory()->create();

    livewire(EditAiAssistant::class, [
        'record' => $aiAssistant->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasFormErrors($errors);
})->with(
    [
        'name required' => [
            CreateAiAssistantRequestFactory::new()->state(['name' => null]),
            ['name' => 'required']
        ],
        'name string' => [
            CreateAiAssistantRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string']
        ],
        'name max' => [
            CreateAiAssistantRequestFactory::new()->state(['name' => str()->random(256)]),
            ['name' => 'max']
        ],
        'model required' => [
            CreateAiAssistantRequestFactory::new()->state(['model' => null]),
            ['model' => 'required']
        ],
        'model must be correct enum' => [
            CreateAiAssistantRequestFactory::new()->state(['model' => AiModel::OpenAiGpt4]),
            ['model' => Enum::class]
        ],
        'description required' => [
            CreateAiAssistantRequestFactory::new()->state(['description' => null]),
            ['description' => 'required']
        ],
        'instructions required' => [
            CreateAiAssistantRequestFactory::new()->state(['instructions' => null]),
            ['instructions' => 'required']
        ],
        'instructions max' => [
            CreateAiAssistantRequestFactory::new()->withOverMaxInstructions(),
            ['instructions' => 'max']
        ],
    ]
);
