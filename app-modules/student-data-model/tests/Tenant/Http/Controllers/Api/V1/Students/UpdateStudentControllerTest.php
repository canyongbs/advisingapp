<?php

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use AdvisingApp\StudentDataModel\Tests\Tenant\Http\Controllers\Api\V1\Students\RequestFactories\UpdateStudentRequestFactory;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;

beforeEach(function () {
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $student = Student::factory()->create();
    $updateStudentRequestData = UpdateStudentRequestFactory::new()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.update', ['student' => $student], false), $updateStudentRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.update', ['student' => $student], false), $updateStudentRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.*.update');
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.update', ['student' => $student], false), $updateStudentRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.update', ['student' => $student], false), $updateStudentRequestData);

    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.update', ['student' => $student], false), $updateStudentRequestData)
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

    $response = putJson(route('api.v1.students.update', ['student' => $student], false), $updateStudentRequestData);
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

    if (isset($updateStudentRequestData['sms_opt_out'])) {
        expect($response['data']['sms_opt_out'] ?? null)
            ->toBe($updateStudentRequestData['sms_opt_out']);
    }

    if (isset($updateStudentRequestData['email_bounce'])) {
        expect($response['data']['email_bounce'] ?? null)
            ->toBe($updateStudentRequestData['email_bounce']);
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

    if (isset($updateStudentRequestData['f_e_term'])) {
        expect($response['data']['f_e_term'] ?? null)
            ->toBe($updateStudentRequestData['f_e_term']);
    }

    if (isset($updateStudentRequestData['mr_e_term'])) {
        expect($response['data']['mr_e_term'] ?? null)
            ->toBe($updateStudentRequestData['mr_e_term']);
    }
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

    $response = putJson(route('api.v1.students.update', ['student' => $student], false), $updateStudentRequestData);
    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        $invalidAttribute => [$validationMessage],
    ]);
})->with([
    '`first` is max 255 characters' => [['first' => str_repeat('a', 256)], 'first', 'The first may not be greater than 255 characters.'],
    '`last` is max 255 characters' => [['last' => str_repeat('a', 256)], 'last', 'The last may not be greater than 255 characters.'],
    '`full_name` is max 255 characters' => [['full_name' => str_repeat('a', 256)], 'full_name', 'The full name may not be greater than 255 characters.'],
    '`preferred` is max 255 characters' => [['preferred' => str_repeat('a', 256)], 'preferred', 'The preferred may not be greater than 255 characters.'],
    '`birthdate` is a valid date' => [['birthdate' => 'not-a-date'], 'birthdate', 'The birthdate is not a valid date.'],
    '`birthdate` is Y-m-d format' => [['birthdate' => '2020/01/01'], 'birthdate', 'The birthdate does not match the format Y-m-d.'],
    '`hsgrad` is numeric' => [['hsgrad' => 'not-a-number'], 'hsgrad', 'The hsgrad must be a number.'],
    '`gender` is max 255 characters' => [['gender' => str_repeat('a', 256)], 'gender', 'The gender may not be greater than 255 characters.'],
    '`sms_opt_out` is boolean' => [['sms_opt_out' => 'not-boolean'], 'sms_opt_out', 'The sms opt out field must be true or false.'],
    '`email_bounce` is boolean' => [['email_bounce' => 'not-boolean'], 'email_bounce', 'The email bounce field must be true or false.'],
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
    '`f_e_term` is max 255 characters' => [['f_e_term' => str_repeat('a', 256)], 'f_e_term', 'The f e term may not be greater than 255 characters.'],
    '`mr_e_term` is max 255 characters' => [['mr_e_term' => str_repeat('a', 256)], 'mr_e_term', 'The mr e term may not be greater than 255 characters.'],
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

    $response = putJson(route('api.v1.students.update', ['student' => $student, 'include' => $relationship], false), $updateStudentRequestData);
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data'])
        ->toHaveKey($responseKey);
})->with([
    '`primaryEmailAddress`' => ['primary_email_address', 'primary_email_address'],
]);
