<?php

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use AdvisingApp\StudentDataModel\Tests\Tenant\Http\Controllers\Api\V1\Students\StudentEmailAddresses\RequestFactories\UpdateStudentEmailAddressRequestFactory;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patchJson;

beforeEach(function () {
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $student = Student::factory()->create();
    $studentEmailAddress = StudentEmailAddress::factory()
        ->for($student)
        ->create();
    $updateStudentEmailAddressRequestData = UpdateStudentEmailAddressRequestFactory::new()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.students.email-addresses.update', ['student' => $student, 'studentEmailAddress' => $studentEmailAddress], false), $updateStudentEmailAddressRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.students.email-addresses.update', ['student' => $student, 'studentEmailAddress' => $studentEmailAddress], false), $updateStudentEmailAddressRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.*.update');
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.students.email-addresses.update', ['student' => $student, 'studentEmailAddress' => $studentEmailAddress], false), $updateStudentEmailAddressRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.students.email-addresses.update', ['student' => $student, 'studentEmailAddress' => $studentEmailAddress], false), $updateStudentEmailAddressRequestData);

    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.students.email-addresses.update', ['student' => $student, 'studentEmailAddress' => $studentEmailAddress], false), $updateStudentEmailAddressRequestData)
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
    $studentEmailAddress = StudentEmailAddress::factory()
        ->for($student)
        ->create();
    $updateStudentEmailAddressRequestData = UpdateStudentEmailAddressRequestFactory::new()->create();

    $response = patchJson(route('api.v1.students.email-addresses.update', ['student' => $student, 'studentEmailAddress' => $studentEmailAddress], false), $updateStudentEmailAddressRequestData);
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);

    if (isset($updateStudentEmailAddressRequestData['address'])) {
        expect($response['data']['address'] ?? null)
            ->toBe($updateStudentEmailAddressRequestData['address']);
    }

    if (isset($updateStudentEmailAddressRequestData['type'])) {
        expect($response['data']['type'] ?? null)
            ->toBe($updateStudentEmailAddressRequestData['type']);
    }

    if (isset($updateStudentEmailAddressRequestData['order'])) {
        expect($response['data']['order'] ?? null)
            ->toBe($updateStudentEmailAddressRequestData['order']);
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
    $studentEmailAddress = StudentEmailAddress::factory()
        ->for($student)
        ->create();
    $updateStudentEmailAddressRequestData = UpdateStudentEmailAddressRequestFactory::new()->create($requestAttributes);

    $response = patchJson(route('api.v1.students.email-addresses.update', ['student' => $student, 'studentEmailAddress' => $studentEmailAddress], false), $updateStudentEmailAddressRequestData);
    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        $invalidAttribute => [$validationMessage],
    ]);
})->with([
    // requestAttributes, invalidAttribute, validationMessage, before
    '`address` is a valid email' => [['address' => 'not-an-email'], 'address', 'The address must be a valid email address.'],
    '`type` is max 255 characters' => [['type' => str_repeat('a', 256)], 'type', 'The type may not be greater than 255 characters.'],
    '`order` is integer' => [['order' => 'not-an-integer'], 'order', 'The order must be an integer.'],
]);
