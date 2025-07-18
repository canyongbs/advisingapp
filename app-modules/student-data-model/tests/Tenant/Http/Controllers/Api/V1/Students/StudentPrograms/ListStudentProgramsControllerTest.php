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

use AdvisingApp\StudentDataModel\Models\Program;
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
    getJson(route('api.v1.students.programs.index', ['student' => $student], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.students.programs.index', ['student' => $student], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.view']);
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.students.programs.index', ['student' => $student], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.view', 'program.view-any']);
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.students.programs.index', ['student' => $student], false))
        ->assertOk();
});

it('returns a paginated list of student programs', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.view', 'program.view-any']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();

    Program::factory()
        ->for($student, 'student')
        ->count(3)
        ->create();

    $response = getJson(route('api.v1.students.programs.index', ['student' => $student], false));
    $response->assertOk();
    $response->assertJsonStructure([
        'data', 'links', 'meta',
    ]);

    expect($response['data'])
        ->toHaveCount(3);
});

it('can filter student programs by all attributes', function (string $requestKey, mixed $requestValue, array $includedAttributes, array $excludedAttributes, string $responseKey, mixed $responseValue) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.view', 'program.view-any']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();

    Program::factory()->for($student, 'student')->create($includedAttributes);
    // Seed two programs with the same non-matching attributes
    Program::factory()->for($student, 'student')->create($excludedAttributes);
    Program::factory()->for($student, 'student')->create($excludedAttributes);

    $response = getJson(route('api.v1.students.programs.index', ['student' => $student, 'filter' => [$requestKey => $requestValue]], false));
    $response->assertOk();

    expect($response['data'][0][$responseKey])
        ->toBe($responseValue);
    expect($response['meta']['total'])
        ->toBe(1);
})->with([
    // requestKey, requestValue, includedAttributes, excludedAttributes, responseKey, responseValue
    '`acad_career`' => ['acad_career', 'UG', ['acad_career' => 'UG'], ['acad_career' => 'Deploma'], 'acad_career', 'UG'],
    '`division`' => ['division', 'Science', ['division' => 'Science'], ['division' => 'Computer'], 'division', 'Science'],
    '`acad_plan`' => ['acad_plan', 'Standard', ['acad_plan' => 'Standard'], ['acad_plan' => 'Platinum'], 'acad_plan', 'Standard'],
    '`prog_status`' => ['prog_status', 'Active', ['prog_status' => 'Active'], ['prog_status' => 'Closed'], 'prog_status', 'Active'],
    '`cum_gpa`' => ['cum_gpa', 3.5, ['cum_gpa' => 3.5], ['cum_gpa' => 5.5], 'cum_gpa', 3.5],
    '`semester`' => ['semester', 'Fall 2023', ['semester' => 'Fall 2023'], ['semester' => 'Summer 2024'], 'semester', 'Fall 2023'],
    '`descr`' => ['descr', 'Computer Science Program', ['descr' => 'Computer Science Program'], ['descr' => 'Mech. Enginner'], 'descr', 'Computer Science Program'],
    '`foi`' => ['foi', 'API Developing', ['foi' => 'API Developing'], ['foi' => 'Web Developer'], 'foi', 'API Developing'],
    '`change_dt`' => ['change_dt', '2023-10-01T00:00:00.000000Z', ['change_dt' => '2023-10-01T00:00:00.000000Z'], ['change_dt' => '2024-10-11T19:05:00.000000Z'], 'change_dt', '2023-10-01T00:00:00.000000Z'],
    '`declare_dt`' => ['declare_dt', '2023-10-01T00:00:00.000000Z', ['declare_dt' => '2023-10-01T00:00:00.000000Z'], ['declare_dt' => '2024-10-11T19:05:00.000000Z'], 'declare_dt', '2023-10-01T00:00:00.000000Z'],
]);

dataset('sorts', [
    // requestKey, firstAttributes, secondAttributes, responseKey, responseFirstValue, responseSecondValue
    '`acad_career`' => ['acad_career', ['acad_career' => 'A'], ['acad_career' => 'B'], 'acad_career', 'A', 'B'],
    '`division`' => ['division', ['division' => 'A'], ['division' => 'B'], 'division', 'A', 'B'],
    '`acad_plan`' => ['acad_plan', ['acad_plan' => 'A'], ['acad_plan' => 'B'], 'acad_plan', 'A', 'B'],
    '`prog_status`' => ['prog_status', ['prog_status' => 'A'], ['prog_status' => 'B'], 'prog_status', 'A', 'B'],
    '`cum_gpa`' => ['cum_gpa', ['cum_gpa' => 3.5], ['cum_gpa' => 4.7], 'cum_gpa', 3.5, 4.7],
    '`semester`' => ['semester', ['semester' => 'A'], ['semester' => 'B'], 'semester', 'A', 'B'],
    '`descr`' => ['descr', ['descr' => 'A'], ['descr' => 'B'], 'descr', 'A', 'B'],
    '`foi`' => ['foi', ['foi' => 'A'], ['foi' => 'B'], 'foi', 'A', 'B'],
    '`change_dt`' => ['change_dt', ['change_dt' => '2023-01-01T00:00:00.000000Z'], ['change_dt' => '2023-01-02T00:00:00.000000Z'], 'change_dt', '2023-01-01T00:00:00.000000Z', '2023-01-02T00:00:00.000000Z'],
    '`declare_dt`' => ['declare_dt', ['declare_dt' => '2023-01-01T00:00:00.000000Z'], ['declare_dt' => '2023-01-02T00:00:00.000000Z'], 'declare_dt', '2023-01-01T00:00:00.000000Z', '2023-01-02T00:00:00.000000Z'],
]);

it('can sort student programs by all attributes ascending', function (string $requestKey, array $firstAttributes, array $secondAttributes, string $responseKey, mixed $responseFirstValue, mixed $responseSecondValue) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.view', 'program.view-any']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();

    Program::factory()->for($student, 'student')->create($firstAttributes);
    Program::factory()->for($student, 'student')->create($secondAttributes);

    $response = getJson(route('api.v1.students.programs.index', ['student' => $student, 'sort' => $requestKey], false));
    $response->assertOk();

    expect($response['data'][0][$responseKey])
        ->toBe($responseFirstValue);
    expect($response['data'][1][$responseKey])
        ->toBe($responseSecondValue);
})->with('sorts');

it('can sort student programs by all attributes descending', function (string $requestKey, array $firstAttributes, array $secondAttributes, string $responseKey, mixed $responseFirstValue, mixed $responseSecondValue) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.view', 'program.view-any']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();

    Program::factory()->for($student, 'student')->create($firstAttributes);
    Program::factory()->for($student, 'student')->create($secondAttributes);

    $response = getJson(route('api.v1.students.programs.index', ['student' => $student, 'sort' => '-' . $requestKey], false));
    $response->assertOk();

    expect($response['data'][0][$responseKey])
        ->toBe($responseSecondValue);
    expect($response['data'][1][$responseKey])
        ->toBe($responseFirstValue);
})->with('sorts');
