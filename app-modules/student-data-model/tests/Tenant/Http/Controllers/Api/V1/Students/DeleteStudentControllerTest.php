<?php

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;

beforeEach(function () {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $student = Student::factory()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.students.delete', $student, false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.students.delete', $student, false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.*.delete');
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.students.delete', $student, false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.delete']);
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.students.delete', $student, false))
        ->assertForbidden();

    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.delete']);
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.students.delete', $student, false))
        ->assertNoContent();
});

it('deletes a student', function () {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.delete']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();

    $response = deleteJson(route('api.v1.students.delete', $student, false));
    $response->assertNoContent();

    assertSoftDeleted(Student::class, [
        'sisid' => $student->sisid,
    ]);
});
