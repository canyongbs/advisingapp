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

use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\CreateStudent;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use App\DataTransferObjects\AutocompletedAddress;
use App\Models\User;
use Filament\Forms\Components\Repeater;

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

it('selecting an address in the AddressInput sets the address fields', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $studentSettings = app(ManageStudentConfigurationSettings::class);
    $studentSettings->is_enabled = true;
    $studentSettings->save();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.create');
    actingAs($user);

    $component = livewire(CreateStudent::class);

    $addresses = $component->get('data.addresses');
    $itemUuid = array_key_first($addresses);

    $component
        ->call('callSchemaComponentMethod', "form.addresses.{$itemUuid}.address", 'reactOnItemSelectedFromJs', [
            'item' => [
                'value' => '123 Main St, City, ST, 12345, US',
                'label' => '123 Main St, City, ST, 12345, US',
                'data' => [
                    'data' => new AutocompletedAddress(
                        line1: '123 Main St',
                        city: 'City',
                        state: 'ST',
                        postalCode: '12345',
                        country: 'US',
                        label: '123 Main St, City, ST, 12345, US',
                    ),
                ],
            ],
        ])
        ->assertFormSet([
            "addresses.{$itemUuid}.line_1" => '123 Main St',
            "addresses.{$itemUuid}.city" => 'City',
            "addresses.{$itemUuid}.state" => 'ST',
            "addresses.{$itemUuid}.postal" => '12345',
            "addresses.{$itemUuid}.country" => 'US',
        ]);
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
