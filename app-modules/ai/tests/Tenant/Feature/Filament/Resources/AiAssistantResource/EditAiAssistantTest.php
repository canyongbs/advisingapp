<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Filament\Resources\AiAssistants\Pages\EditAiAssistant;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Tests\Tenant\Feature\Filament\Resources\AiAssistantResource\RequestFactories\EditAiAssistantRequestFactory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Features\AdvisorNameCitextFeature;
use App\Filament\Forms\Components\UserSelect;
use App\Models\Authenticatable;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

/** @var array<LicenseType> $licenses */
$licenses = [
    LicenseType::ConversationalAi,
];

$permissions = [
    'assistant_custom.view-any',
    'assistant_custom.*.update',
];

it('cannot render without a license', function () use ($permissions): void {
    actingAs(user(
        permissions: $permissions
    ));

    $aiAssistant = AiAssistant::factory()->create();

    get(EditAiAssistant::getUrl([
        'record' => $aiAssistant->getRouteKey(),
    ]))
        ->assertForbidden();
});

it('cannot render without permissions', function () use ($licenses): void {
    actingAs(user(
        licenses: $licenses
    ));

    $aiAssistant = AiAssistant::factory()->create();

    get(EditAiAssistant::getUrl([
        'record' => $aiAssistant->getRouteKey(),
    ]))
        ->assertForbidden();
});

it('cannot render without proper features enabled', function () use ($licenses, $permissions): void {
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

it('can render', function () use ($licenses, $permissions): void {
    actingAs(user(
        licenses: $licenses,
        permissions: $permissions
    ));

    $settings = app(LicenseSettings::class);

    $settings->data->addons->employeeAdvisors = true;

    $settings->save();

    $aiAssistant = AiAssistant::factory()->create();

    get(EditAiAssistant::getUrl([
        'record' => $aiAssistant->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('can edit a record without changing its creator', function (bool $isSuperAdmin) use ($licenses, $permissions): void {
    Storage::fake('s3');

    $editor = user(
        licenses: $licenses,
        permissions: $permissions
    );

    if ($isSuperAdmin) {
        $editor->assignRole(Authenticatable::SUPER_ADMIN_ROLE);
    }

    actingAs($editor);

    $settings = app(LicenseSettings::class);

    $settings->data->addons->employeeAdvisors = true;

    $settings->save();

    $creator = User::factory()->create();

    $aiAssistant = AiAssistant::factory()->for($creator, 'createdBy')->create();

    expect($aiAssistant->created_by_id)->toBe($creator->getKey());

    $request = collect(EditAiAssistantRequestFactory::new()->create());

    Livewire::test(EditAiAssistant::class, [
        'record' => $aiAssistant->getRouteKey(),
    ])
        ->assertFormFieldDoesNotExist('created_by_id')
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertHasNoErrors();

    expect($aiAssistant->refresh()->created_by_id)->toBe($creator->getKey());

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
})->with([
    'regular user' => false,
    'super admin' => true,
]);

it('validates the inputs', function (EditAiAssistantRequestFactory $data, array $errors) use ($licenses, $permissions): void {
    Storage::fake('s3');

    actingAs(user(
        licenses: $licenses,
        permissions: $permissions
    ));

    $settings = app(LicenseSettings::class);

    $settings->data->addons->employeeAdvisors = true;

    $settings->save();

    $request = collect(EditAiAssistantRequestFactory::new($data)->create());

    $aiAssistant = AiAssistant::factory()->create();

    Livewire::test(EditAiAssistant::class, [
        'record' => $aiAssistant->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasFormErrors($errors);
})->with(
    [
        'name required' => [
            EditAiAssistantRequestFactory::new()->state(['name' => null]),
            ['name' => 'required'],
        ],
        'name string' => [
            EditAiAssistantRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            EditAiAssistantRequestFactory::new()->state(['name' => str()->random(256)]),
            ['name' => 'max'],
        ],
        'model required' => [
            EditAiAssistantRequestFactory::new()->state(['model' => null]),
            ['model' => 'required'],
        ],
        'model must be correct enum' => [
            EditAiAssistantRequestFactory::new()->state(['model' => AiModel::OpenAiGpt4o]),
            ['model'],
        ],
        'description required' => [
            EditAiAssistantRequestFactory::new()->state(['description' => null]),
            ['description' => 'required'],
        ],
        'instructions required' => [
            EditAiAssistantRequestFactory::new()->state(['instructions' => null]),
            ['instructions' => 'required'],
        ],
        'instructions max' => [
            EditAiAssistantRequestFactory::new()->withOverMaxInstructions(),
            ['instructions' => 'max'],
        ],
    ]
);

it('ai_assistant_confidential_users UserSelect does not show admin users in options by default on EditAiAssistant', function () use ($licenses, $permissions): void {
    actingAs(user(
        licenses: $licenses,
        permissions: $permissions
    ));

    $licenseSettings = app(LicenseSettings::class);
    $licenseSettings->data->addons->employeeAdvisors = true;
    $licenseSettings->save();

    $regularUser = User::factory()->create();
    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    $aiAssistant = AiAssistant::factory()->create(['is_confidential' => true]);

    Livewire::test(EditAiAssistant::class, [
        'record' => $aiAssistant->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertFormFieldExists('ai_assistant_confidential_users', checkFieldUsing: function (UserSelect $field) use ($regularUser, $adminUser): bool {
            return ! empty($field->getSearchResults($regularUser->name))
                && empty($field->getSearchResults($adminUser->name));
        });
});

it('ai_assistant_confidential_users UserSelect shows all users when filter_admins_from_selection config is false on EditAiAssistant', function () use ($licenses, $permissions): void {
    Config::set('app.filter_admins_from_selection', false);

    actingAs(user(
        licenses: $licenses,
        permissions: $permissions
    ));

    $licenseSettings = app(LicenseSettings::class);
    $licenseSettings->data->addons->employeeAdvisors = true;
    $licenseSettings->save();

    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    $aiAssistant = AiAssistant::factory()->create(['is_confidential' => true]);

    Livewire::test(EditAiAssistant::class, [
        'record' => $aiAssistant->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertFormFieldExists('ai_assistant_confidential_users', checkFieldUsing: function (UserSelect $field) use ($adminUser): bool {
            return ! empty($field->getSearchResults($adminUser->name));
        });
});

it('does not allow renaming an assistant to a name matching another one case-insensitively', function () use ($licenses, $permissions): void {
    Storage::fake('s3');

    actingAs(user(
        licenses: $licenses,
        permissions: $permissions
    ));

    $settings = app(LicenseSettings::class);
    $settings->data->addons->employeeAdvisors = true;
    $settings->save();

    AiAssistant::factory()->create(['name' => 'Admissions Helper']);
    $aiAssistant = AiAssistant::factory()->create(['name' => 'Financial Aid Helper']);

    $request = collect(EditAiAssistantRequestFactory::new()->create(['name' => 'admissions helper']));

    Livewire::test(EditAiAssistant::class, [
        'record' => $aiAssistant->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);
});

it('allows renaming an assistant to a name freed up by a soft-deleted one and to its own name', function () use ($licenses, $permissions): void {
    Storage::fake('s3');

    actingAs(user(
        licenses: $licenses,
        permissions: $permissions
    ));

    $settings = app(LicenseSettings::class);
    $settings->data->addons->employeeAdvisors = true;
    $settings->save();

    AiAssistant::factory()->create(['name' => 'Admissions Helper'])->delete();
    $aiAssistant = AiAssistant::factory()->create(['name' => 'Financial Aid Helper']);

    $request = collect(EditAiAssistantRequestFactory::new()->create(['name' => 'admissions helper']));

    Livewire::test(EditAiAssistant::class, [
        'record' => $aiAssistant->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors(['name']);

    expect($aiAssistant->refresh()->name)->toBe('admissions helper');

    $request = collect(EditAiAssistantRequestFactory::new()->create(['name' => 'ADMISSIONS HELPER']));

    Livewire::test(EditAiAssistant::class, [
        'record' => $aiAssistant->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors(['name']);

    expect($aiAssistant->refresh()->name)->toBe('ADMISSIONS HELPER');
});

// TODO: Cleanup Task (advisor-name-citext) - Delete this test (the unique index always applies once the feature flag is removed)
it('does not apply name uniqueness validation before the citext migration activates', function () use ($licenses, $permissions): void {
    Storage::fake('s3');

    AdvisorNameCitextFeature::deactivate();

    actingAs(user(
        licenses: $licenses,
        permissions: $permissions
    ));

    $settings = app(LicenseSettings::class);
    $settings->data->addons->employeeAdvisors = true;
    $settings->save();

    DB::statement('DROP INDEX IF EXISTS ai_assistants_name_unique');

    AiAssistant::factory()->create(['name' => 'Admissions Helper']);
    $aiAssistant = AiAssistant::factory()->create(['name' => 'Financial Aid Helper']);

    $request = collect(EditAiAssistantRequestFactory::new()->create(['name' => 'Admissions Helper']));

    Livewire::test(EditAiAssistant::class, [
        'record' => $aiAssistant->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors(['name']);
});
