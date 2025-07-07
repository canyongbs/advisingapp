<?php

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
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
    $studentEmailAddress = StudentEmailAddress::factory()
        ->for($student)
        ->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.students.email-addresses.delete', ['student' => $student, 'studentEmailAddress' => $studentEmailAddress], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.students.email-addresses.delete', ['student' => $student, 'studentEmailAddress' => $studentEmailAddress], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.*.update');
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.students.email-addresses.delete', ['student' => $student, 'studentEmailAddress' => $studentEmailAddress], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.students.email-addresses.delete', ['student' => $student, 'studentEmailAddress' => $studentEmailAddress], false))
        ->assertForbidden();

    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.students.email-addresses.delete', ['student' => $student, 'studentEmailAddress' => $studentEmailAddress], false))
        ->assertNoContent();
});

it('deletes a student email address', function () {
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

    $response = deleteJson(route('api.v1.students.email-addresses.delete', ['student' => $student, 'studentEmailAddress' => $studentEmailAddress], false));
    $response->assertNoContent();

    assertDatabaseMissing(StudentEmailAddress::class, [
        'id' => $studentEmailAddress->getKey(),
    ]);
});

it('swaps out the current primary email address for a student with another when the primary email address is deleted', function () {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();
    $secondaryEmailAddress = StudentEmailAddress::factory()
        ->for($student)
        ->create();

    $response = deleteJson(route('api.v1.students.email-addresses.delete', ['student' => $student, 'studentEmailAddress' => $student->primaryEmailAddress], false));
    $response->assertNoContent();

    assertDatabaseMissing(StudentEmailAddress::class, [
        'id' => $student->primaryEmailAddress->getKey(),
    ]);

    expect($student->refresh()->primaryEmailAddress()->is($secondaryEmailAddress))
        ->toBeTrue();
});
