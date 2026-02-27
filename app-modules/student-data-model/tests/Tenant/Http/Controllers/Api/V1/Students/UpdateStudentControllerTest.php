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
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use AdvisingApp\StudentDataModel\Tests\Tenant\Http\Controllers\Api\V1\Students\RequestFactories\UpdateStudentRequestFactory;
use App\Models\SystemUser;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patchJson;

beforeEach(function () {
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $student = Student::factory()->create();
    $updateStudentRequestData = UpdateStudentRequestFactory::new()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.students.update', ['student' => $student], false), $updateStudentRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.students.update', ['student' => $student], false), $updateStudentRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.*.update');
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.students.update', ['student' => $student], false), $updateStudentRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.students.update', ['student' => $student], false), $updateStudentRequestData);

    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.students.update', ['student' => $student], false), $updateStudentRequestData)
        ->assertOk();
});

it('updates a student', function () {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();
    $updateStudentRequestData = UpdateStudentRequestFactory::new()->create();

    $response = patchJson(route('api.v1.students.update', ['student' => $student], false), $updateStudentRequestData);
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);

    if (isset($updateStudentRequestData['otherid'])) {
        expect($response['data']['otherid'] ?? null)
            ->toBe($updateStudentRequestData['otherid']);
    }

    if (isset($updateStudentRequestData['first'])) {
        expect($response['data']['first'] ?? null)
            ->toBe($updateStudentRequestData['first']);
    }

    if (isset($updateStudentRequestData['last'])) {
        expect($response['data']['last'] ?? null)
            ->toBe($updateStudentRequestData['last']);
    }

    if (isset($updateStudentRequestData['full_name'])) {
        expect($response['data']['full_name'] ?? null)
            ->toBe($updateStudentRequestData['full_name']);
    }

    if (isset($updateStudentRequestData['preferred'])) {
        expect($response['data']['preferred'] ?? null)
            ->toBe($updateStudentRequestData['preferred']);
    }

    if (isset($updateStudentRequestData['birthdate'])) {
        expect($response['data']['birthdate'] ?? null)
            ->toBe($updateStudentRequestData['birthdate']);
    }

    if (isset($updateStudentRequestData['hsgrad'])) {
        expect($response['data']['hsgrad'] ?? null)
            ->toBe($updateStudentRequestData['hsgrad']);
    }

    if (isset($updateStudentRequestData['gender'])) {
        expect($response['data']['gender'] ?? null)
            ->toBe($updateStudentRequestData['gender']);
    }

    if (isset($updateStudentRequestData['dual'])) {
        expect($response['data']['dual'] ?? null)
            ->toBe($updateStudentRequestData['dual']);
    }

    if (isset($updateStudentRequestData['ferpa'])) {
        expect($response['data']['ferpa'] ?? null)
            ->toBe($updateStudentRequestData['ferpa']);
    }

    if (isset($updateStudentRequestData['firstgen'])) {
        expect($response['data']['firstgen'] ?? null)
            ->toBe($updateStudentRequestData['firstgen']);
    }

    if (isset($updateStudentRequestData['sap'])) {
        expect($response['data']['sap'] ?? null)
            ->toBe($updateStudentRequestData['sap']);
    }

    if (isset($updateStudentRequestData['holds'])) {
        expect($response['data']['holds'] ?? null)
            ->toBe($updateStudentRequestData['holds']);
    }

    if (isset($updateStudentRequestData['dfw'])) {
        expect($response['data']['dfw'] ?? null)
            ->toBe($updateStudentRequestData['dfw']);
    }

    if (isset($updateStudentRequestData['ethnicity'])) {
        expect($response['data']['ethnicity'] ?? null)
            ->toBe($updateStudentRequestData['ethnicity']);
    }

    if (isset($updateStudentRequestData['lastlmslogin'])) {
        expect($response['data']['lastlmslogin'] ?? null)
            ->toBe($updateStudentRequestData['lastlmslogin']);
    }
});

it('updates a student\'s institutional email address', function () {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();

    $studentEmailAddress = StudentEmailAddress::factory()
        ->for($student)
        ->create();

    expect($student->refresh()->primary_email_id)
        ->not->toBe($studentEmailAddress->getKey());

    $response = patchJson(route('api.v1.students.update', ['student' => $student], false), [
        'primary_email_id' => $studentEmailAddress->getKey(),
    ]);
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data']['primary_email_id'] ?? null)
        ->toBe($studentEmailAddress->getKey());
});

it('updates a student\'s primary phone number', function () {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();

    $studentPhoneNumber = StudentPhoneNumber::factory()
        ->for($student)
        ->create();

    expect($student->refresh()->primary_phone_id)
        ->not->toBe($studentPhoneNumber->getKey());

    $response = patchJson(route('api.v1.students.update', ['student' => $student], false), [
        'primary_phone_id' => $studentPhoneNumber->getKey(),
    ]);
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data']['primary_phone_id'] ?? null)
        ->toBe($studentPhoneNumber->getKey());
});

it('validates', function (array $requestAttributes, string $invalidAttribute, string $validationMessage, ?Closure $before = null) {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);

    $before?->call($this);

    $student = Student::factory()->create();
    $updateStudentRequestData = UpdateStudentRequestFactory::new()->create($requestAttributes);

    $response = patchJson(route('api.v1.students.update', ['student' => $student], false), $updateStudentRequestData);
    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        $invalidAttribute => [$validationMessage],
    ]);
})->with([
    // requestAttributes, invalidAttribute, validationMessage, before
    '`first` is max 255 characters' => [['first' => str_repeat('a', 256)], 'first', 'The first may not be greater than 255 characters.'],
    '`last` is max 255 characters' => [['last' => str_repeat('a', 256)], 'last', 'The last may not be greater than 255 characters.'],
    '`full_name` is max 255 characters' => [['full_name' => str_repeat('a', 256)], 'full_name', 'The full name may not be greater than 255 characters.'],
    '`preferred` is max 255 characters' => [['preferred' => str_repeat('a', 256)], 'preferred', 'The preferred may not be greater than 255 characters.'],
    '`birthdate` is a valid date' => [['birthdate' => 'not-a-date'], 'birthdate', 'The birthdate is not a valid date.'],
    '`birthdate` is Y-m-d format' => [['birthdate' => '2020/01/01'], 'birthdate', 'The birthdate does not match the format Y-m-d.'],
    '`hsgrad` is valid date' => [['hsgrad' => 'not-a-date'], 'hsgrad', 'The hsgrad is not a valid date.'],
    '`hsgrad` is Y-m-d format' => [['hsgrad' => '2022/01/01'], 'hsgrad', 'The hsgrad does not match the format Y-m-d.'],
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
    '`primary_email_id` is a valid UUID' => [['primary_email_id' => 'not-a-uuid'], 'primary_email_id', 'The institutional email id must be a valid UUID.'],
    '`primary_email_id` is an existing email address ID' => [['primary_email_id' => (string) Str::orderedUuid()], 'primary_email_id', 'The selected institutional email id is invalid.'],
    '`primary_email_id` is an email address ID for the current student' => [['primary_email_id' => ($primaryEmailId = (string) Str::orderedUuid())], 'primary_email_id', 'The selected institutional email id is invalid.', function () use ($primaryEmailId) {
        StudentEmailAddress::factory()
            ->for(Student::factory())
            ->create([
                'id' => $primaryEmailId,
            ]);
    }],
    '`primary_phone_id` is a valid UUID' => [['primary_phone_id' => 'not-a-uuid'], 'primary_phone_id', 'The primary phone id must be a valid UUID.'],
    '`primary_phone_id` is an existing phone number ID' => [['primary_phone_id' => (string) Str::orderedUuid()], 'primary_phone_id', 'The selected primary phone id is invalid.'],
    '`primary_phone_id` is an phone number ID for the current student' => [['primary_phone_id' => ($primaryPhoneId = (string) Str::orderedUuid())], 'primary_phone_id', 'The selected primary phone id is invalid.', function () use ($primaryPhoneId) {
        StudentPhoneNumber::factory()
            ->for(Student::factory())
            ->create([
                'id' => $primaryPhoneId,
            ]);
    }],
]);

it('can include related student relationships', function (string $relationship, string $responseKey) {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();
    $updateStudentRequestData = UpdateStudentRequestFactory::new()->create();

    $response = patchJson(route('api.v1.students.update', ['student' => $student, 'include' => $relationship], false), $updateStudentRequestData);
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data'])
        ->toHaveKey($responseKey);
})->with([
    // relationship, responseKey
    '`primaryEmailAddress`' => ['primary_email_address', 'primary_email_address'],
    '`primaryPhoneNumber`' => ['primary_phone_number', 'primary_phone_number'],
]);
