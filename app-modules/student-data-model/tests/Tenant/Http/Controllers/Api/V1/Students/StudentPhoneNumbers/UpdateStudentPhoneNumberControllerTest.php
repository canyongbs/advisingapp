<?php

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use AdvisingApp\StudentDataModel\Tests\Tenant\Http\Controllers\Api\V1\Students\StudentPhoneNumbers\RequestFactories\UpdateStudentPhoneNumberRequestFactory;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patchJson;

beforeEach(function () {
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $student = Student::factory()->create();
    $studentPhoneNumber = StudentPhoneNumber::factory()
        ->for($student)
        ->create();
    $updateStudentPhoneNumberRequestData = UpdateStudentPhoneNumberRequestFactory::new()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.students.phone-numbers.update', ['student' => $student, 'studentPhoneNumber' => $studentPhoneNumber], false), $updateStudentPhoneNumberRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.students.phone-numbers.update', ['student' => $student, 'studentPhoneNumber' => $studentPhoneNumber], false), $updateStudentPhoneNumberRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.*.update');
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.students.phone-numbers.update', ['student' => $student, 'studentPhoneNumber' => $studentPhoneNumber], false), $updateStudentPhoneNumberRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.students.phone-numbers.update', ['student' => $student, 'studentPhoneNumber' => $studentPhoneNumber], false), $updateStudentPhoneNumberRequestData);

    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.students.phone-numbers.update', ['student' => $student, 'studentPhoneNumber' => $studentPhoneNumber], false), $updateStudentPhoneNumberRequestData)
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
    $studentPhoneNumber = StudentPhoneNumber::factory()
        ->for($student)
        ->create();
    $updateStudentPhoneNumberRequestData = UpdateStudentPhoneNumberRequestFactory::new()->create();

    $response = patchJson(route('api.v1.students.phone-numbers.update', ['student' => $student, 'studentPhoneNumber' => $studentPhoneNumber], false), $updateStudentPhoneNumberRequestData);
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);

    if (isset($updateStudentPhoneNumberRequestData['address'])) {
        expect($response['data']['address'] ?? null)
            ->toBe($updateStudentPhoneNumberRequestData['address']);
    }

    if (isset($updateStudentPhoneNumberRequestData['type'])) {
        expect($response['data']['type'] ?? null)
            ->toBe($updateStudentPhoneNumberRequestData['type']);
    }

    if (isset($updateStudentPhoneNumberRequestData['order'])) {
        expect($response['data']['order'] ?? null)
            ->toBe($updateStudentPhoneNumberRequestData['order']);
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
    $studentPhoneNumber = StudentPhoneNumber::factory()
        ->for($student)
        ->create();
    $updateStudentPhoneNumberRequestData = UpdateStudentPhoneNumberRequestFactory::new()->create($requestAttributes);

    $response = patchJson(route('api.v1.students.phone-numbers.update', ['student' => $student, 'studentPhoneNumber' => $studentPhoneNumber], false), $updateStudentPhoneNumberRequestData);
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
