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
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\CustomerAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\CreateCustomerAdvisor;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Ai\Tests\RequestFactories\CustomerAdvisorRequestFactory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Features\AdvisorNameCitextFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function PHPUnit\Framework\assertCount;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

use function Tests\asSuperAdmin;

test('Create Customer Advisor is gated with proper access control', function (): void {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    actingAs($user)
        ->get(
            CustomerAdvisorResource::getUrl('create')
        )->assertForbidden();

    Livewire::test(CreateCustomerAdvisor::class)
        ->assertForbidden();

    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.create']);

    actingAs($user)
        ->get(
            CustomerAdvisorResource::getUrl('create')
        )->assertSuccessful();
});

test('can create Customer Advisor', function (): void {
    Storage::fake('s3');

    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    assertDatabaseCount(CustomerAdvisor::class, 0);

    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.create']);

    actingAs($user);

    $customerAdvisor = collect(CustomerAdvisorRequestFactory::new()->create());

    Livewire::test(CreateCustomerAdvisor::class)
        ->fillForm($customerAdvisor->except(['model'])->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, CustomerAdvisor::all());

    assertDatabaseHas(
        CustomerAdvisor::class,
        $customerAdvisor->except([
            'avatar',
            'model',
        ])->toArray()
    );

    assertDatabaseHas(
        Media::class,
        [
            'model_type' => (new (CustomerAdvisor::class))->getMorphClass(),
            'model_id' => CustomerAdvisor::query()->first()->getKey(),
            'collection_name' => 'avatar',
        ]
    );
});

test('Create Customer Advisor validates the inputs', function (CustomerAdvisorRequestFactory $data, array $errors): void {
    Storage::fake('s3');

    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    asSuperAdmin();

    $request = collect(CustomerAdvisorRequestFactory::new($data)->create());

    Livewire::test(CreateCustomerAdvisor::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertDatabaseMissing(
        CustomerAdvisor::class,
        $request->except([
            'avatar',
        ])->toArray()
    );
})->with(
    [
        'name required' => [
            CustomerAdvisorRequestFactory::new()->without('name'),
            ['name' => 'required'],
        ],
        'name string' => [
            CustomerAdvisorRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            CustomerAdvisorRequestFactory::new()->state(['name' => str()->random(257)]),
            ['name' => 'max'],
        ],
        'description required' => [
            CustomerAdvisorRequestFactory::new()->without('description'),
            ['description' => 'required'],
        ],
        'description max' => [
            CustomerAdvisorRequestFactory::new()->state(['description' => str()->random(65537)]),
            ['description' => 'max'],
        ],
        'model required' => [
            CustomerAdvisorRequestFactory::new()->state(['model' => null]),
            ['model' => 'required'],
        ],
        'model must be correct enum' => [
            CustomerAdvisorRequestFactory::new()->state(['model' => AiModel::OpenAiGpt4o]),
            ['model'],
        ],
    ]
);

test('Create Customer Advisor does not allow a name matching another advisor case-insensitively', function (): void {
    Storage::fake('s3');

    $settings = app(LicenseSettings::class);
    $settings->data->addons->customerAdvisors = true;
    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.create']);

    actingAs($user);

    CustomerAdvisor::factory()->create(['name' => 'Admissions Helper']);

    $request = collect(CustomerAdvisorRequestFactory::new()->create(['name' => 'admissions helper']));

    Livewire::test(CreateCustomerAdvisor::class)
        ->fillForm($request->except(['model'])->toArray())
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);

    assertDatabaseCount(CustomerAdvisor::class, 1);
});

test('Create Customer Advisor allows a name freed up by a soft-deleted advisor', function (): void {
    Storage::fake('s3');

    $settings = app(LicenseSettings::class);
    $settings->data->addons->customerAdvisors = true;
    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.create']);

    actingAs($user);

    CustomerAdvisor::factory()->create(['name' => 'Admissions Helper'])->delete();

    $request = collect(CustomerAdvisorRequestFactory::new()->create(['name' => 'admissions helper']));

    Livewire::test(CreateCustomerAdvisor::class)
        ->fillForm($request->except(['model'])->toArray())
        ->call('create')
        ->assertHasNoFormErrors(['name']);

    assertDatabaseHas(CustomerAdvisor::class, ['name' => 'admissions helper', 'deleted_at' => null]);
});

// TODO: Cleanup Task (advisor-name-citext) - Delete this test (the unique index always applies once the feature flag is removed)
test('Create Customer Advisor does not apply name uniqueness validation before the citext migration activates', function (): void {
    Storage::fake('s3');

    AdvisorNameCitextFeature::deactivate();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->customerAdvisors = true;
    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.create']);

    actingAs($user);

    DB::statement('DROP INDEX IF EXISTS customer_advisors_name_unique');

    CustomerAdvisor::factory()->create(['name' => 'Admissions Helper']);

    $request = collect(CustomerAdvisorRequestFactory::new()->create(['name' => 'Admissions Helper']));

    Livewire::test(CreateCustomerAdvisor::class)
        ->fillForm($request->except(['model'])->toArray())
        ->call('create')
        ->assertHasNoFormErrors(['name']);
});

test('Create Customer Advisor groups the introductory message fields in their own section', function (): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->customerAdvisors = true;
    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();
    $user->givePermissionTo(['customer_advisor.view-any', 'customer_advisor.create']);

    actingAs($user);

    $components = Livewire::test(CreateCustomerAdvisor::class)
        ->instance()
        ->getSchema('form')
        ->getComponents(withOriginalKeys: true);

    $sections = array_values(array_filter($components, fn (mixed $component): bool => $component instanceof Section));

    expect($sections)->toHaveCount(1);

    $section = $sections[0];

    expect($section->getHeading())->toBe('Introductory Message')
        ->and(array_map(
            function (mixed $field): string {
                assert($field instanceof Field);

                return $field->getName();
            },
            $section->getDefaultChildComponents(),
        ))
        ->toBe(['is_introductory_message_enabled', 'is_introductory_message_dynamic', 'introductory_message']);
});
