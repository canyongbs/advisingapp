<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
