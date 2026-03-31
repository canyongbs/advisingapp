<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\CreateStudent;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use App\DataTransferObjects\AutocompletedAddress;
use App\Filament\Forms\Components\AddressInput;
use App\Models\User;
use App\Services\AwsGeoPlacesService;
use DefStudio\SearchableInput\Forms\Components\SearchableInput;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;
use Mockery\MockInterface;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('requires proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    actingAs($user);

    livewire(CreateStudent::class)
        ->assertForbidden();

    $studentSettings = app(ManageStudentConfigurationSettings::class);
    $studentSettings->is_enabled = true;
    $studentSettings->save();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.create');

    livewire(CreateStudent::class)
        ->assertOk();
});

it('can fetch autocomplete addresses from the AddressInput component', function () {
    /** @phpstan-ignore method.notFound */
    $this->mock(AwsGeoPlacesService::class, function (MockInterface $mock) {
        /** @phpstan-ignore method.notFound */
        $mock->shouldReceive('autocompleteComponents')
            ->once()
            ->with('123 Main')
            ->andReturn([
                new AutocompletedAddress(
                    line1: '123 Main St',
                    city: 'City',
                    state: 'ST',
                    postalCode: '12345',
                    country: 'US',
                    label: '123 Main St, City, ST, 12345, US',
                ),
            ]);
    });

    $input = AddressInput::make();
    $form = Schema::make()->components([$input]);
    /** @var SearchableInput $component */
    $component = $form->getComponent('address');

    $resultsShort = $component->getSearchResultsForJs('12');

    $results = $component->getSearchResultsForJs('123 Main');
    /** @phpstan-ignore argument.templateType */
    expect($results)->toHaveCount(1);
    /** @phpstan-ignore argument.templateType */
    expect($resultsShort)->toBeEmpty();

    $firstResult = $results[0];

    expect($firstResult)->toBeArray();
    expect($firstResult)->toHaveKeys(['label', 'data']);

    expect($firstResult['label'])->toBe('123 Main St, City, ST, 12345, US');

    $data = $firstResult['data']['data'] ?? [];
    expect($data['line1'] ?? null)->toBe('123 Main St');
    expect($data['city'] ?? null)->toBe('City');
    expect($data['state'] ?? null)->toBe('ST');
    expect($data['postalCode'] ?? null)->toBe('12345');
    expect($data['country'] ?? null)->toBe('US');
});

it('can create a student with an address', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $studentSettings = app(ManageStudentConfigurationSettings::class);
    $studentSettings->is_enabled = true;
    $studentSettings->save();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.create');
    actingAs($user);

    $uuid = (string) str()->uuid();
    $undoRepeaterFake = Repeater::fake();

    livewire(CreateStudent::class)
        ->fillForm([
            'sisid' => '12345678',
            'first' => 'John',
            'last' => 'Doe',
            'addresses' => [
                $uuid => [
                    'line_1' => '123 Test St',
                    'city' => 'Testville',
                    'state' => 'TX',
                    'postal' => '78701',
                    'country' => 'US',
                    'type' => 'Home',
                ],
            ],
            'emailAddresses' => [
                $uuid => [
                    'address' => 'john.doe@test.com',
                ],
            ],
            'phoneNumbers' => [
                $uuid => [
                    'number' => '+15555551234',
                    'type' => 'Mobile',
                    'can_receive_sms' => true,
                    'sms_opt_out_phone_number' => false,
                ],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $student = Student::where('sisid', '12345678')->first();
    expect($student)->not->toBeNull();
    expect($student->first)->toBe('John');
    expect($student->last)->toBe('Doe');
    expect($student->addresses()->count())->toBe(1);

    $address = $student->addresses()->first();
    expect($address->line_1)->toBe('123 Test St');
    expect($address->city)->toBe('Testville');
    expect($address->state)->toBe('TX');
    expect($address->postal)->toBe('78701');
    expect($address->country)->toBe('US');

    $undoRepeaterFake();
});
