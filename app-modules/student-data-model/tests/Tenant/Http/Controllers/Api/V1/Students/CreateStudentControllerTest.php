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

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use AdvisingApp\StudentDataModel\Tests\Tenant\Http\Controllers\Api\V1\Students\RequestFactories\CreateStudentRequestFactory;
use AdvisingApp\StudentDataModel\Tests\Tenant\Http\Controllers\Api\V1\Students\StudentEmailAddresses\RequestFactories\CreateStudentEmailAddressRequestFactory;
use AdvisingApp\StudentDataModel\Tests\Tenant\Http\Controllers\Api\V1\Students\StudentPhoneNumbers\RequestFactories\CreateStudentPhoneNumberRequestFactory;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

beforeEach(function () {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $createStudentRequestData = CreateStudentRequestFactory::new()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.create', [], false), $createStudentRequestData)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.create', [], false), $createStudentRequestData)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.create');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.create', [], false), $createStudentRequestData)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.create']);
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.create', [], false), $createStudentRequestData)
        ->assertForbidden();

    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.create']);
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.create', [], false), $createStudentRequestData)
        ->assertCreated();
});

it('creates a student', function () {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.create']);
    Sanctum::actingAs($user, ['api']);

    $createStudentRequestData = CreateStudentRequestFactory::new()->create();

    $response = postJson(route('api.v1.students.create', [], false), $createStudentRequestData);
    $response->assertCreated();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data']['sisid'] ?? null)
        ->toBe($createStudentRequestData['sisid']);

    if (isset($createStudentRequestData['otherid'])) {
        expect($response['data']['otherid'] ?? null)
            ->toBe($createStudentRequestData['otherid']);
    }

    expect($response['data']['first'] ?? null)
        ->toBe($createStudentRequestData['first']);

    expect($response['data']['last'] ?? null)
        ->toBe($createStudentRequestData['last']);

    expect($response['data']['full_name'] ?? null)
        ->toBe($createStudentRequestData['full_name']);

    if (isset($createStudentRequestData['preferred'])) {
        expect($response['data']['preferred'] ?? null)
            ->toBe($createStudentRequestData['preferred']);
    }

    if (isset($createStudentRequestData['birthdate'])) {
        expect($response['data']['birthdate'] ?? null)
            ->toBe($createStudentRequestData['birthdate']);
    }

    if (isset($createStudentRequestData['hsgrad'])) {
        expect($response['data']['hsgrad'] ?? null)
            ->toBe($createStudentRequestData['hsgrad']);
    }

    if (isset($createStudentRequestData['gender'])) {
        expect($response['data']['gender'] ?? null)
            ->toBe($createStudentRequestData['gender']);
    }

    if (isset($createStudentRequestData['dual'])) {
        expect($response['data']['dual'] ?? null)
            ->toBe($createStudentRequestData['dual']);
    }

    if (isset($createStudentRequestData['ferpa'])) {
        expect($response['data']['ferpa'] ?? null)
            ->toBe($createStudentRequestData['ferpa']);
    }

    if (isset($createStudentRequestData['firstgen'])) {
        expect($response['data']['firstgen'] ?? null)
            ->toBe($createStudentRequestData['firstgen']);
    }

    if (isset($createStudentRequestData['sap'])) {
        expect($response['data']['sap'] ?? null)
            ->toBe($createStudentRequestData['sap']);
    }

    if (isset($createStudentRequestData['holds'])) {
        expect($response['data']['holds'] ?? null)
            ->toBe($createStudentRequestData['holds']);
    }

    if (isset($createStudentRequestData['dfw'])) {
        expect($response['data']['dfw'] ?? null)
            ->toBe($createStudentRequestData['dfw']);
    }

    if (isset($createStudentRequestData['ethnicity'])) {
        expect($response['data']['ethnicity'] ?? null)
            ->toBe($createStudentRequestData['ethnicity']);
    }

    if (isset($createStudentRequestData['lastlmslogin'])) {
        expect($response['data']['lastlmslogin'] ?? null)
            ->toBe($createStudentRequestData['lastlmslogin']);
    }

    assertDatabaseHas(Student::class, [
        'sisid' => $createStudentRequestData['sisid'],
    ]);
});

it('creates student email addresses', function () {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.create']);
    Sanctum::actingAs($user, ['api']);

    $createStudentRequestData = CreateStudentRequestFactory::new()->create([
        'email_addresses' => [
            CreateStudentEmailAddressRequestFactory::new()->create(),
            CreateStudentEmailAddressRequestFactory::new()->create(),
        ],
    ]);

    $response = postJson(route('api.v1.students.create', ['include' => 'email_addresses,primary_email_address'], false), $createStudentRequestData);
    $response->assertCreated();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data']['email_addresses'] ?? null)
        ->toBeArray()
        ->toHaveCount(2);

    expect($response['data']['email_addresses'][0]['address'] ?? null)
        ->toBe($createStudentRequestData['email_addresses'][0]['address']);

    if (isset($createStudentRequestData['email_addresses'][0]['type'])) {
        expect($response['data']['email_addresses'][0]['type'] ?? null)
            ->toBe($createStudentRequestData['email_addresses'][0]['type']);
    }

    expect($response['data']['email_addresses'][1]['address'] ?? null)
        ->toBe($createStudentRequestData['email_addresses'][1]['address']);

    if (isset($createStudentRequestData['email_addresses'][1]['type'])) {
        expect($response['data']['email_addresses'][1]['type'] ?? null)
            ->toBe($createStudentRequestData['email_addresses'][1]['type']);
    }

    expect($response['data']['primary_email_id'])
        ->toBe($response['data']['email_addresses'][0]['id']);

    expect($response['data']['primary_email_address'] ?? null)
        ->toBeArray();

    expect($response['data']['primary_email_address']['address'] ?? null)
        ->toBe($response['data']['email_addresses'][0]['address']);

    if (isset($createStudentRequestData['email_addresses'][0]['type'])) {
        expect($response['data']['primary_email_address']['type'] ?? null)
            ->toBe($createStudentRequestData['email_addresses'][0]['type']);
    }
});

it('creates student phone numbers', function () {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.create']);
    Sanctum::actingAs($user, ['api']);

    $createStudentRequestData = CreateStudentRequestFactory::new()->create([
        'phone_numbers' => [
            CreateStudentPhoneNumberRequestFactory::new()->create(),
            CreateStudentPhoneNumberRequestFactory::new()->create(),
        ],
    ]);

    $response = postJson(route('api.v1.students.create', ['include' => 'phone_numbers,primary_phone_number'], false), $createStudentRequestData);
    $response->assertCreated();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data']['phone_numbers'] ?? null)
        ->toBeArray()
        ->toHaveCount(2);

    expect($response['data']['phone_numbers'][0]['number'] ?? null)
        ->toBe($createStudentRequestData['phone_numbers'][0]['number']);

    if (isset($createStudentRequestData['phone_numbers'][0]['type'])) {
        expect($response['data']['phone_numbers'][0]['type'] ?? null)
            ->toBe($createStudentRequestData['phone_numbers'][0]['type']);
    }

    expect($response['data']['phone_numbers'][1]['number'] ?? null)
        ->toBe($createStudentRequestData['phone_numbers'][1]['number']);

    if (isset($createStudentRequestData['phone_numbers'][1]['type'])) {
        expect($response['data']['phone_numbers'][1]['type'] ?? null)
            ->toBe($createStudentRequestData['phone_numbers'][1]['type']);
    }

    expect($response['data']['primary_phone_id'])
        ->toBe($response['data']['phone_numbers'][0]['id']);

    expect($response['data']['primary_phone_number'] ?? null)
        ->toBeArray();

    expect($response['data']['primary_phone_number']['number'] ?? null)
        ->toBe($response['data']['phone_numbers'][0]['number']);

    if (isset($createStudentRequestData['phone_numbers'][0]['type'])) {
        expect($response['data']['primary_phone_number']['type'] ?? null)
            ->toBe($createStudentRequestData['phone_numbers'][0]['type']);
    }
});

it('validates', function (array $requestAttributes, string $invalidAttribute, string $validationMessage, ?Closure $before = null) {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.create']);
    Sanctum::actingAs($user, ['api']);

    $before?->call($this);

    $createStudentRequestData = CreateStudentRequestFactory::new()->create([
        'email_addresses' => [
            CreateStudentEmailAddressRequestFactory::new()->create(),
        ],
        ...$requestAttributes,
    ]);

    $response = postJson(route('api.v1.students.create', [], false), $createStudentRequestData);
    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        $invalidAttribute => [$validationMessage],
    ]);
})->with([
    // requestAttributes, invalidAttribute, validationMessage, before
    '`sisid` is required' => [['sisid' => null], 'sisid', 'The sisid field is required.'],
    '`sisid` is max 255 characters' => [['sisid' => str_repeat('a', 256)], 'sisid', 'The sisid may not be greater than 255 characters.'],
    '`sisid` is alpha dash' => [['sisid' => 'invalid sisid!'], 'sisid', 'The sisid may only contain letters, numbers, and dashes.'],
    '`sisid` is unique' => [['sisid' => 'existing-sisid'], 'sisid', 'The sisid has already been taken.', function () {
        Student::factory()->create(['sisid' => 'existing-sisid']);
    }],
    '`otherid` is max 255 characters' => [['otherid' => str_repeat('a', 256)], 'otherid', 'The otherid may not be greater than 255 characters.'],
    '`first` is required' => [['first' => null], 'first', 'The first field is required.'],
    '`first` is max 255 characters' => [['first' => str_repeat('a', 256)], 'first', 'The first may not be greater than 255 characters.'],
    '`last` is required' => [['last' => null], 'last', 'The last field is required.'],
    '`last` is max 255 characters' => [['last' => str_repeat('a', 256)], 'last', 'The last may not be greater than 255 characters.'],
    '`full_name` is required' => [['full_name' => null], 'full_name', 'The full name field is required.'],
    '`full_name` is max 255 characters' => [['full_name' => str_repeat('a', 256)], 'full_name', 'The full name may not be greater than 255 characters.'],
    '`preferred` is max 255 characters' => [['preferred' => str_repeat('a', 256)], 'preferred', 'The preferred may not be greater than 255 characters.'],
    '`birthdate` is a valid date' => [['birthdate' => 'not-a-date'], 'birthdate', 'The birthdate is not a valid date.'],
    '`birthdate` is Y-m-d format' => [['birthdate' => '2020/01/01'], 'birthdate', 'The birthdate does not match the format Y-m-d.'],
    '`hsgrad` is a valid date' => [['hsgrad' => 'not-a-date'], 'hsgrad', 'The hsgrad is not a valid date.'],
    '`hsgrad` is Y-m-d format' => [['hsgrad' => '2020/01/01'], 'hsgrad', 'The hsgrad does not match the format Y-m-d.'],
    '`gender` is max 255 characters' => [['gender' => str_repeat('a', 256)], 'gender', 'The gender may not be greater than 255 characters.'],
    '`dual` is boolean' => [['dual' => 'not-boolean'], 'dual', 'The dual field must be true or false.'],
    '`ferpa` is boolean' => [['ferpa' => 'not-boolean'], 'ferpa', 'The ferpa field must be true or false.'],
    '`firstgen` is boolean' => [['firstgen' => 'not-boolean'], 'firstgen', 'The firstgen field must be true or false.'],
    '`sap` is boolean' => [['sap' => 'not-boolean'], 'sap', 'The sap field must be true or false.'],
    '`holds` is max 255 characters' => [['holds' => str_repeat('a', 256)], 'holds', 'The holds may not be greater than 255 characters.'],
    '`dfw` is a valid date' => [['dfw' => 'not-a-date'], 'dfw', 'The dfw is not a valid date.'],
    '`dfw` is Y-m-d format' => [['dfw' => '2020/01/01'], 'dfw', 'The dfw does not match the format Y-m-d.'],
    '`ethnicity` is max 255 characters' => [['ethnicity' => str_repeat('a', 256)], 'ethnicity', 'The ethnicity may not be greater than 255 characters.'],
    '`lastlmslogin` is a valid date' => [['lastlmslogin' => 'not-a-date'], 'lastlmslogin', 'The lastlmslogin is not a valid date.'],
    '`lastlmslogin` is Y-m-d H:i:s format' => [['lastlmslogin' => '2020-01-01'], 'lastlmslogin', 'The lastlmslogin does not match the format Y-m-d H:i:s.'],
    '`email_addresses` is an array' => [['email_addresses' => 'not-an-array'], 'email_addresses', 'The email addresses must be an array.'],
    '`email_addresses.*` is an array' => [['email_addresses' => ['not-an-array']], 'email_addresses.0', 'The email_addresses.0 must be an array.'],
    '`email_addresses.*.address` is required' => [['email_addresses' => [['address' => null]]], 'email_addresses.0.address', 'The email_addresses.0.address field is required.'],
    '`email_addresses.*.address` is a valid email' => [['email_addresses' => [['address' => 'not-an-email']]], 'email_addresses.0.address', 'The email_addresses.0.address must be a valid email address.'],
    '`email_addresses.*.type` is max 255 characters' => [['email_addresses' => [['address' => 'test@example.com', 'type' => str_repeat('a', 256)]]], 'email_addresses.0.type', 'The email_addresses.0.type may not be greater than 255 characters.'],
    '`phone_numbers` is an array' => [['phone_numbers' => 'not-an-array'], 'phone_numbers', 'The phone numbers must be an array.'],
    '`phone_numbers.*` is an array' => [['phone_numbers' => ['not-an-array']], 'phone_numbers.0', 'The phone_numbers.0 must be an array.'],
    '`phone_numbers.*.number` is required' => [['phone_numbers' => [['number' => null]]], 'phone_numbers.0.number', 'The phone_numbers.0.number field is required.'],
    '`phone_numbers.*.type` is max 255 characters' => [['phone_numbers' => [['number' => '123456789', 'type' => str_repeat('a', 256)]]], 'phone_numbers.0.type', 'The phone_numbers.0.type may not be greater than 255 characters.'],
    '`phone_numbers.*.order` is a valid integer' => [['phone_numbers' => [['number' => '123456789', 'order' => 'not-integer']]], 'phone_numbers.0.order', 'The phone_numbers.0.order must be an integer.'],
    '`phone_numbers.*.ext` is a valid integer' => [['phone_numbers' => [['number' => '123456789', 'ext' => 'not-integer']]], 'phone_numbers.0.ext', 'The phone_numbers.0.ext must be an integer.'],
    '`phone_numbers.*.can_receive_sms` is boolean' => [['phone_numbers' => [['number' => '123456789', 'can_receive_sms' => 'not-boolean']]], 'phone_numbers.0.can_receive_sms', 'The phone_numbers.0.can_receive_sms field must be true or false.'],
]);

it('can include related student relationships', function (string $relationship, string $responseKey) {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.create']);
    Sanctum::actingAs($user, ['api']);

    $createStudentRequestData = CreateStudentRequestFactory::new()->create();

    $response = postJson(route('api.v1.students.create', [], false), $createStudentRequestData);
    $response->assertCreated();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data'])
        ->not()->toHaveKey($responseKey);

    $createStudentRequestData = CreateStudentRequestFactory::new()->create();

    $response = postJson(route('api.v1.students.create', ['include' => $relationship], false), $createStudentRequestData);
    $response->assertCreated();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data'])
        ->toHaveKey($responseKey);
})->with([
    // relationship, responseKey
    '`emailAddresses`' => ['email_addresses', 'email_addresses'],
    '`primaryEmailAddress`' => ['primary_email_address', 'primary_email_address'],
    '`phoneNumbers`' => ['phone_numbers', 'phone_numbers'],
    '`primaryPhoneNumber`' => ['primary_phone_number', 'primary_phone_number'],
]);
