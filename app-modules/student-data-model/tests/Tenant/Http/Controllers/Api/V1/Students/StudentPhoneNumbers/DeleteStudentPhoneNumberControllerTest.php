<?php

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;

beforeEach(function () {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $student = Student::factory()->create();
    $studentPhoneNumber = StudentPhoneNumber::factory()
        ->for($student)
        ->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.students.phone-numbers.delete', ['student' => $student, 'studentPhoneNumber' => $studentPhoneNumber], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.students.phone-numbers.delete', ['student' => $student, 'studentPhoneNumber' => $studentPhoneNumber], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.*.update');
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.students.phone-numbers.delete', ['student' => $student, 'studentPhoneNumber' => $studentPhoneNumber], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.students.phone-numbers.delete', ['student' => $student, 'studentPhoneNumber' => $studentPhoneNumber], false))
        ->assertForbidden();

    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.students.phone-numbers.delete', ['student' => $student, 'studentPhoneNumber' => $studentPhoneNumber], false))
        ->assertNoContent();
});

it('deletes a student phone number', function () {
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

    $response = deleteJson(route('api.v1.students.phone-numbers.delete', ['student' => $student, 'studentPhoneNumber' => $studentPhoneNumber], false));
    $response->assertNoContent();

    assertDatabaseMissing(StudentPhoneNumber::class, [
        'id' => $studentPhoneNumber->getKey(),
    ]);
});

it('swaps out the current primary phone number for a student with another when the primary phone number is deleted', function () {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();
    $secondaryPhoneNumber = StudentPhoneNumber::factory()
        ->for($student)
        ->create();

    $response = deleteJson(route('api.v1.students.phone-numbers.delete', ['student' => $student, 'studentPhoneNumber' => $student->primaryPhoneNumber], false));
    $response->assertNoContent();

    assertDatabaseMissing(StudentPhoneNumber::class, [
        'id' => $student->primaryPhoneNumber->getKey(),
    ]);

    expect($student->refresh()->primaryPhoneNumber()->is($secondaryPhoneNumber))
        ->toBeTrue();
});
