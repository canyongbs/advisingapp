<?php

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
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
    $student = Student::factory()->create();
    $createStudentPhoneNumberRequestData = CreateStudentPhoneNumberRequestFactory::new()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.phone-numbers.create', ['student' => $student], false), $createStudentPhoneNumberRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.phone-numbers.create', ['student' => $student], false), $createStudentPhoneNumberRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.*.update');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.phone-numbers.create', ['student' => $student], false), $createStudentPhoneNumberRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.phone-numbers.create', ['student' => $student], false), $createStudentPhoneNumberRequestData);

    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.phone-numbers.create', ['student' => $student], false), $createStudentPhoneNumberRequestData)
        ->assertCreated();
});

it('creates a student phone number', function () {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();
    $createStudentPhoneNumberRequestData = CreateStudentPhoneNumberRequestFactory::new()->create();

    $response = postJson(route('api.v1.students.phone-numbers.create', ['student' => $student], false), $createStudentPhoneNumberRequestData);
    $response->assertCreated();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data']['sisid'] ?? null)
        ->toBe($student->sisid);

    expect($response['data']['number'] ?? null)
        ->toBe($createStudentPhoneNumberRequestData['number']);

    if (isset($createStudentPhoneNumberRequestData['type'])) {
        expect($response['data']['type'] ?? null)
            ->toBe($createStudentPhoneNumberRequestData['type']);
    }

    if (isset($createStudentPhoneNumberRequestData['order'])) {
        expect($response['data']['order'] ?? null)
            ->toBe($createStudentPhoneNumberRequestData['order']);
    }

    if (isset($createStudentPhoneNumberRequestData['ext'])) {
        expect($response['data']['ext'] ?? null)
            ->toBe($createStudentPhoneNumberRequestData['ext']);
    }

    if (isset($createStudentPhoneNumberRequestData['can_receive_sms'])) {
        expect($response['data']['can_receive_sms'] ?? false)
            ->toBe($createStudentPhoneNumberRequestData['can_receive_sms']);
    }

    assertDatabaseHas(StudentPhoneNumber::class, [
        'sisid' => $student->sisid,
        'number' => $createStudentPhoneNumberRequestData['number'],
    ]);
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
    $createStudentEmailAddressRequestData = CreateStudentPhoneNumberRequestFactory::new()->create($requestAttributes);

    $response = postJson(route('api.v1.students.phone-numbers.create', ['student' => $student], false), $createStudentEmailAddressRequestData);
    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        $invalidAttribute => [$validationMessage],
    ]);
})->with([
    // requestAttributes, invalidAttribute, validationMessage, before
    '`number` is required' => [['number' => null], 'number', 'The number field is required.'],
    '`type` is max 255 characters' => [['type' => str_repeat('a', 256)], 'type', 'The type may not be greater than 255 characters.'],
    '`order` is integer' => [['order' => 'not-an-integer'], 'order', 'The order must be an integer.'],
    '`ext` is integer' => [['ext' => 'not-an-integer'], 'ext', 'The ext must be an integer.'],
    '`can_receive_sms` is boolean' => [['can_receive_sms' => 'not-boolean'], 'can_receive_sms', 'The can receive sms field must be true or false.'],
]);
