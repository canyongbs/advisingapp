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
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages\CreateQnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Tests\RequestFactories\QnaAdvisorRequestFactory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

use function Tests\asSuperAdmin;

test('Create QnA Advisor is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

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
});

test('can create QnA Advisor', function () {
    Storage::fake('s3');

    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    assertDatabaseCount(QnaAdvisor::class, 0);

    $user->givePermissionTo(['qna_advisor.view-any', 'qna_advisor.create']);

    actingAs($user);

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

test('Create QnA Advisor validates the inputs', function ($data, $errors) {
    Storage::fake('s3');

    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

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
        'model required' => [
            QnaAdvisorRequestFactory::new()->state(['model' => null]),
            ['model' => 'required'],
        ],
        'model must be correct enum' => [
            QnaAdvisorRequestFactory::new()->state(['model' => AiModel::OpenAiGpt4o]),
            ['model'],
        ],
    ]
);
