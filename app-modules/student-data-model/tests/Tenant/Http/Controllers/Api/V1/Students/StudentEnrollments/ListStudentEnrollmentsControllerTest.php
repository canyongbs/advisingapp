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

use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

beforeEach(function () {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $user = SystemUser::factory()->create();
    $student = Student::factory()->create();
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.students.enrollments.index', ['student' => $student], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.students.enrollments.index', ['student' => $student], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.view']);
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.students.enrollments.index', ['student' => $student], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.view', 'enrollment.view-any']);
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.students.enrollments.index', ['student' => $student], false))
        ->assertOk();
});

it('returns a paginated list of student enrollments', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.view', 'enrollment.view-any']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();

    Enrollment::factory()
        ->for($student, 'student')
        ->count(3)
        ->create();

    $response = getJson(route('api.v1.students.enrollments.index', ['student' => $student], false));
    $response->assertOk();
    $response->assertJsonStructure([
        'data', 'links', 'meta',
    ]);

    expect($response['data'])
        ->toHaveCount(3);
});

it('can filter student enrollments by all attributes', function (string $requestKey, mixed $requestValue, array $includedAttributes, array $excludedAttributes, string $responseKey, mixed $responseValue) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.view', 'enrollment.view-any']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();

    Enrollment::factory()->for($student, 'student')->create($includedAttributes);
    // Seed two enrollments with the same non-matching attributes
    Enrollment::factory()->for($student, 'student')->create($excludedAttributes);
    Enrollment::factory()->for($student, 'student')->create($excludedAttributes);

    $response = getJson(route('api.v1.students.enrollments.index', ['student' => $student, 'filter' => [$requestKey => $requestValue]], false));
    $response->assertOk();

    expect($response['data'][0][$responseKey])
        ->toBe($responseValue);
    expect($response['meta']['total'])
        ->toBe(1);
})->with([
    // requestKey, requestValue, includedAttributes, excludedAttributes, responseKey, responseValue
    '`division`' => ['division', 'ABC01', ['division' => 'ABC01'], ['division' => 'ABC02'], 'division', 'ABC01'],
    '`class_nbr`' => ['class_nbr', '19130', ['class_nbr' => '19130'], ['class_nbr' => '19140'], 'class_nbr', '19130'],
    '`crse_grade_off`' => ['crse_grade_off', 'A', ['crse_grade_off' => 'A'], ['crse_grade_off' => 'B'], 'crse_grade_off', 'A'],
    '`unt_taken`' => ['unt_taken', 2, ['unt_taken' => 2], ['unt_taken' => 4], 'unt_taken', 2],
    '`unt_earned`' => ['unt_earned', 3, ['unt_earned' => 3], ['unt_earned' => 1], 'unt_earned', 3],
    '`last_upd_dt_stmp`' => ['last_upd_dt_stmp', '2023-10-01T00:00:00.000000Z', ['last_upd_dt_stmp' => '2023-10-01T00:00:00.000000Z'], ['last_upd_dt_stmp' => '2024-10-11T19:05:00.000000Z'], 'last_upd_dt_stmp', '2023-10-01T00:00:00.000000Z'],
    '`section`' => ['section', '1234', ['section' => '1234'], ['section' => '5678'], 'section', '1234'],
    '`name`' => ['name', 'College Algebra', ['name' => 'College Algebra'], ['name' => 'Introduction to Mathematics'], 'name', 'College Algebra'],
    '`department`' => ['department', 'Business', ['department' => 'Business'], ['department' => 'Administration'], 'department', 'Business'],
    '`faculty_name`' => ['faculty_name', 'Dr. Smith', ['faculty_name' => 'Dr. Smith'], ['faculty_name' => 'Dr. Johnson'], 'faculty_name', 'Dr. Smith'],
    '`faculty_email`' => ['faculty_email', 'smith@edu.in', ['faculty_email' => 'smith@edu.in'], ['faculty_email' => 'johnson@edu.in'], 'faculty_email', 'smith@edu.in'],
    '`semester_code`' => ['semester_code', '4201', ['semester_code' => '4201'], ['semester_code' => '4202'], 'semester_code', '4201'],
    '`semester_name`' => ['semester_name', 'Fall 2006', ['semester_name' => 'Fall 2006'], ['semester_name' => 'Spring Cohort A 2006'], 'semester_name', 'Fall 2006'],
    '`start_date`' => ['start_date', '2024-10-01T00:00:00.000000Z', ['start_date' => '2024-10-01T00:00:00.000000Z'], ['start_date' => '2024-10-12T19:05:00.000000Z'], 'start_date', '2024-10-01T00:00:00.000000Z'],
    '`end_date`' => ['end_date', '2024-10-10T00:00:00.000000Z', ['end_date' => '2024-10-10T00:00:00.000000Z'], ['end_date' => '2024-10-20T19:05:00.000000Z'], 'end_date', '2024-10-10T00:00:00.000000Z'],
]);

dataset('sorts', [
    // requestKey, firstAttributes, secondAttributes, responseKey, responseFirstValue, responseSecondValue
    '`division`' => ['division', ['division' => 'ABC01'], ['division' => 'ABC02'], 'division', 'ABC01', 'ABC02'],
    '`class_nbr`' => ['class_nbr', ['class_nbr' => '19130'], ['class_nbr' => '19131'], 'class_nbr', '19130', '19131'],
    '`crse_grade_off`' => ['crse_grade_off', ['crse_grade_off' => 'A'], ['crse_grade_off' => 'B'], 'crse_grade_off', 'A', 'B'],
    '`unt_taken`' => ['unt_taken', ['unt_taken' => 1], ['unt_taken' => 2], 'unt_taken', 1, 2],
    '`unt_earned`' => ['unt_earned', ['unt_earned' => 2], ['unt_earned' => 3], 'unt_earned', 2, 3],
    '`last_upd_dt_stmp`' => ['last_upd_dt_stmp', ['last_upd_dt_stmp' => '2024-10-01T00:00:00.000000Z'], ['last_upd_dt_stmp' => '2024-10-10T00:00:00.000000Z'], 'last_upd_dt_stmp', '2024-10-01T00:00:00.000000Z', '2024-10-10T00:00:00.000000Z'],
    '`section`' => ['section', ['section' => '1234'], ['section' => '5678'], 'section', '1234', '5678'],
    '`name`' => ['name', ['name' => 'College Algebra'], ['name' => 'Introduction to Mathematics'], 'name', 'College Algebra', 'Introduction to Mathematics'],
    '`department`' => ['department', ['department' => 'Business'], ['department' => 'Business Administration'], 'department', 'Business', 'Business Administration'],
    '`faculty_name`' => ['faculty_name', ['faculty_name' => 'Dr. Johnson'], ['faculty_name' => 'Dr. Smith'], 'faculty_name', 'Dr. Johnson', 'Dr. Smith'],
    '`faculty_email`' => ['faculty_email', ['faculty_email' => 'johnson@example.com'], ['faculty_email' => 'smith@example.com'], 'faculty_email', 'johnson@example.com', 'smith@example.com'],
    '`semester_code`' => ['semester_code', ['semester_code' => '4201'], ['semester_code' => '4202'], 'semester_code', '4201', '4202'],
    '`semester_name`' => ['semester_name', ['semester_name' => 'Fall 2006'], ['semester_name' => 'Spring Cohort A 2006'], 'semester_name', 'Fall 2006', 'Spring Cohort A 2006'],
    '`start_date`' => ['start_date', ['start_date' => '2024-10-02T00:00:00.000000Z'], ['start_date' => '2024-10-15T00:00:00.000000Z'], 'start_date', '2024-10-02T00:00:00.000000Z', '2024-10-15T00:00:00.000000Z'],
    '`end_date`' => ['end_date', ['end_date' => '2024-10-02T00:00:00.000000Z'], ['end_date' => '2024-10-03T00:00:00.000000Z'], 'end_date', '2024-10-02T00:00:00.000000Z', '2024-10-03T00:00:00.000000Z'],
]);

it('can sort student enrollments by all attributes ascending', function (string $requestKey, array $firstAttributes, array $secondAttributes, string $responseKey, mixed $responseFirstValue, mixed $responseSecondValue) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.view', 'enrollment.view-any']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();

    Enrollment::factory()->for($student, 'student')->create($firstAttributes);
    Enrollment::factory()->for($student, 'student')->create($secondAttributes);

    $response = getJson(route('api.v1.students.enrollments.index', ['student' => $student, 'sort' => $requestKey], false));
    $response->assertOk();

    expect($response['data'][0][$responseKey])
        ->toBe($responseFirstValue);
    expect($response['data'][1][$responseKey])
        ->toBe($responseSecondValue);
})->with('sorts');

it('can sort student enrollments by all attributes descending', function (string $requestKey, array $firstAttributes, array $secondAttributes, string $responseKey, mixed $responseFirstValue, mixed $responseSecondValue) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.view', 'enrollment.view-any']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();

    Enrollment::factory()->for($student, 'student')->create($firstAttributes);
    Enrollment::factory()->for($student, 'student')->create($secondAttributes);

    $response = getJson(route('api.v1.students.enrollments.index', ['student' => $student, 'sort' => '-' . $requestKey], false));
    $response->assertOk();

    expect($response['data'][0][$responseKey])
        ->toBe($responseSecondValue);
    expect($response['data'][1][$responseKey])
        ->toBe($responseFirstValue);
})->with('sorts');
