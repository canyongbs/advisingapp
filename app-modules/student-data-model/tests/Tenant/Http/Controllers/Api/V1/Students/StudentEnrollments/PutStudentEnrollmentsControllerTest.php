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
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use AdvisingApp\StudentDataModel\Tests\Tenant\Http\Controllers\Api\V1\Students\StudentEnrollments\RequestFactories\StudentEnrollmentRequestFactory;
use App\Models\SystemUser;
use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;

beforeEach(function () {
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $student = Student::factory()->create();

    $createStudentEnrollmentRequestData = [
        'enrollments' => [StudentEnrollmentRequestFactory::new()->create()],
    ];

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.enrollments.put', ['student' => $student], false), $createStudentEnrollmentRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.enrollments.put', ['student' => $student], false), $createStudentEnrollmentRequestData)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('enrollment.view-any');
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.enrollments.put', ['student' => $student], false), $createStudentEnrollmentRequestData)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('enrollment.create');
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.enrollments.put', ['student' => $student], false), $createStudentEnrollmentRequestData)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('enrollment.*.update');
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.enrollments.put', ['student' => $student], false), $createStudentEnrollmentRequestData)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('enrollment.*.delete');
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.enrollments.put', ['student' => $student], false), $createStudentEnrollmentRequestData)
        ->assertForbidden();

    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'enrollment.view-any', 'enrollment.create', 'enrollment.*.update', 'enrollment.*.delete']);
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.enrollments.put', ['student' => $student], false), $createStudentEnrollmentRequestData)
        ->assertOk();
});

it('creates a student program', function () {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $student = Student::factory()->create();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'enrollment.view-any', 'enrollment.create', 'enrollment.*.update', 'enrollment.*.delete']);
    Sanctum::actingAs($user, ['api']);

    $createStudentEnrollmentRequestData = [
        'enrollments' => [StudentEnrollmentRequestFactory::new()->create()],
    ];

    $response = putJson(route('api.v1.students.enrollments.put', ['student' => $student], false), $createStudentEnrollmentRequestData);
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data'][0]['division'])
        ->toBe($createStudentEnrollmentRequestData['enrollments'][0]['division']);

    expect($response['data'][0]['class_nbr'])
        ->toBe($createStudentEnrollmentRequestData['enrollments'][0]['class_nbr']);

    expect($response['data'][0]['crse_grade_off'])
        ->toBe($createStudentEnrollmentRequestData['enrollments'][0]['crse_grade_off']);

    expect($response['data'][0]['unt_taken'])
        ->toBe($createStudentEnrollmentRequestData['enrollments'][0]['unt_taken']);

    expect($response['data'][0]['unt_earned'])
        ->toBe($createStudentEnrollmentRequestData['enrollments'][0]['unt_earned']);

    expect($response['data'][0]['section'])
        ->toBe($createStudentEnrollmentRequestData['enrollments'][0]['section']);

    expect($response['data'][0]['name'])
        ->toBe($createStudentEnrollmentRequestData['enrollments'][0]['name']);

    if (isset($createStudentEnrollmentRequestData['enrollments'][0]['department'])) {
        expect($response['data'][0]['department'])
            ->toBe($createStudentEnrollmentRequestData['enrollments'][0]['department']);
    }

    expect($response['data'][0]['faculty_name'])
        ->toBe($createStudentEnrollmentRequestData['enrollments'][0]['faculty_name']);

    expect($response['data'][0]['faculty_email'])
        ->toBe($createStudentEnrollmentRequestData['enrollments'][0]['faculty_email']);

    if (isset($createStudentEnrollmentRequestData['enrollments'][0]['semester_code'])) {
        expect($response['data'][0]['semester_code'])
            ->toBe($createStudentEnrollmentRequestData['enrollments'][0]['semester_code']);
    }

    if (isset($createStudentEnrollmentRequestData['enrollments'][0]['semester_name'])) {
        expect($response['data'][0]['semester_name'])
            ->toBe($createStudentEnrollmentRequestData['enrollments'][0]['semester_name']);
    }

    expect(Carbon::parse($response['data'][0]['last_upd_dt_stmp'])->toDateTimeString())
        ->toBe(Carbon::parse($createStudentEnrollmentRequestData['enrollments'][0]['last_upd_dt_stmp'])->toDateTimeString());

    if (isset($createStudentEnrollmentRequestData['enrollments'][0]['start_date'])) {
        expect(Carbon::parse($response['data'][0]['start_date'])->toDateTimeString())
            ->toBe(Carbon::parse($createStudentEnrollmentRequestData['enrollments'][0]['start_date'])->toDateTimeString());
    }

    if (isset($createStudentEnrollmentRequestData['enrollments'][0]['end_date'])) {
        expect(Carbon::parse($response['data'][0]['end_date'])->toDateTimeString())
            ->toBe(Carbon::parse($createStudentEnrollmentRequestData['enrollments'][0]['end_date'])->toDateTimeString());
    }

    assertDatabaseHas('enrollments', [
        'sisid' => $student->sisid,
        'division' => $createStudentEnrollmentRequestData['enrollments'][0]['division'],
        'class_nbr' => $createStudentEnrollmentRequestData['enrollments'][0]['class_nbr'],
        'crse_grade_off' => $createStudentEnrollmentRequestData['enrollments'][0]['crse_grade_off'],
        'unt_taken' => $createStudentEnrollmentRequestData['enrollments'][0]['unt_taken'],
        'unt_earned' => $createStudentEnrollmentRequestData['enrollments'][0]['unt_earned'],
        'section' => $createStudentEnrollmentRequestData['enrollments'][0]['section'],
        'name' => $createStudentEnrollmentRequestData['enrollments'][0]['name'],
        'last_upd_dt_stmp' => Carbon::parse($createStudentEnrollmentRequestData['enrollments'][0]['last_upd_dt_stmp'])->toDateTimeString(),
    ]);
});

it('validates', function (array $requestAttributes, string $invalidAttribute, string $validationMessage, ?Closure $before = null) {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $student = Student::factory()->create();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'enrollment.view-any', 'enrollment.create', 'enrollment.*.update', 'enrollment.*.delete']);
    Sanctum::actingAs($user, ['api']);

    $before?->call($this);

    $createStudentEnrollmentRequestData = [
        'enrollments' => [
            StudentEnrollmentRequestFactory::new()->create(),
        ],
        ...$requestAttributes,
    ];

    $response = putJson(route('api.v1.students.enrollments.put', ['student' => $student], false), $createStudentEnrollmentRequestData);
    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        $invalidAttribute => [$validationMessage],
    ]);
})
    ->with([
        '`enrollments.*.division` max' => [
            ['enrollments' => [['division' => str_repeat('a', 256)]]],
            'enrollments.0.division',
            'The enrollments.0.division may not be greater than 255 characters.',
        ],
        '`enrollments.*.division` must be a string' => [
            ['enrollments' => [['division' => 1]]],
            'enrollments.0.division',
            'The enrollments.0.division must be a string.',
        ],
        '`enrollments.*.class_nbr` max' => [
            ['enrollments' => [['class_nbr' => str_repeat('a', 256)]]],
            'enrollments.0.class_nbr',
            'The enrollments.0.class_nbr may not be greater than 255 characters.',
        ],
        '`enrollments.*.class_nbr` must be a string' => [
            ['enrollments' => [['class_nbr' => 1]]],
            'enrollments.0.class_nbr',
            'The enrollments.0.class_nbr must be a string.',
        ],
        '`enrollments.*.crse_grade_off` must be a string' => [
            ['enrollments' => [['crse_grade_off' => 1]]],
            'enrollments.0.crse_grade_off',
            'The enrollments.0.crse_grade_off must be a string.',
        ],
        '`enrollments.*.crse_grade_off` max' => [
            ['enrollments' => [['crse_grade_off' => str_repeat('a', 256)]]],
            'enrollments.0.crse_grade_off',
            'The enrollments.0.crse_grade_off may not be greater than 255 characters.',
        ],
        '`enrollments.*.unt_taken` must be a number' => [
            ['enrollments' => [['unt_taken' => str_repeat('a', 256)]]],
            'enrollments.0.unt_taken',
            'The enrollments.0.unt_taken must be a number.',
        ],
        '`enrollments.*.unt_earned` must be a number' => [
            ['enrollments' => [['unt_earned' => str_repeat('a', 256)]]],
            'enrollments.0.unt_earned',
            'The enrollments.0.unt_earned must be a number.',
        ],
        '`last_upd_dt_stmp` is a valid date' => [
            ['enrollments' => [['last_upd_dt_stmp' => 'not-a-date']]],
            'enrollments.0.last_upd_dt_stmp',
            'The enrollments.0.last_upd_dt_stmp is not a valid date.',
        ],
        '`last_upd_dt_stmp` is Y-m-d H:i:s format' => [
            ['enrollments' => [['last_upd_dt_stmp' => '2020-01-01']]],
            'enrollments.0.last_upd_dt_stmp',
            'The enrollments.0.last_upd_dt_stmp does not match the format Y-m-d H:i:s.',
        ],
        '`enrollments.*.section` max' => [
            ['enrollments' => [['section' => str_repeat('a', 256)]]],
            'enrollments.0.section',
            'The enrollments.0.section may not be greater than 255 characters.',
        ],
        '`enrollments.*.section` must be a string' => [
            ['enrollments' => [['section' => 1]]],
            'enrollments.0.section',
            'The enrollments.0.section must be a string.',
        ],
        '`enrollments.*.name` max' => [
            ['enrollments' => [['name' => str_repeat('a', 256)]]],
            'enrollments.0.name',
            'The enrollments.0.name may not be greater than 255 characters.',
        ],
        '`enrollments.*.name` must be a string' => [
            ['enrollments' => [['name' => 1]]],
            'enrollments.0.name',
            'The enrollments.0.name must be a string.',
        ],
        '`enrollments.*.faculty_name` max' => [
            ['enrollments' => [['faculty_name' => str_repeat('a', 256)]]],
            'enrollments.0.faculty_name',
            'The enrollments.0.faculty_name may not be greater than 255 characters.',
        ],
        '`enrollments.*.faculty_name` must be a string' => [
            ['enrollments' => [['faculty_name' => 1]]],
            'enrollments.0.faculty_name',
            'The enrollments.0.faculty_name must be a string.',
        ],
        '`enrollments.*.faculty_email` max' => [
            ['enrollments' => [['faculty_email' => 'test@']]],
            'enrollments.0.faculty_email',
            'The enrollments.0.faculty_email must be a valid email address.',
        ],
        '`enrollments.*.semester_code` max' => [
            ['enrollments' => [['semester_code' => str_repeat('a', 256)]]],
            'enrollments.0.semester_code',
            'The enrollments.0.semester_code may not be greater than 255 characters.',
        ],
        '`enrollments.*.semester_code` must be a string' => [
            ['enrollments' => [['semester_code' => 1]]],
            'enrollments.0.semester_code',
            'The enrollments.0.semester_code must be a string.',
        ],
        '`enrollments.*.semester_name` max' => [
            ['enrollments' => [['semester_name' => str_repeat('a', 256)]]],
            'enrollments.0.semester_name',
            'The enrollments.0.semester_name may not be greater than 255 characters.',
        ],
        '`enrollments.*.semester_name` must be a string' => [
            ['enrollments' => [['semester_name' => 1]]],
            'enrollments.0.semester_name',
            'The enrollments.0.semester_name must be a string.',
        ],
        '`start_date` is a valid date' => [
            ['enrollments' => [['start_date' => 'not-a-date']]],
            'enrollments.0.start_date',
            'The enrollments.0.start_date is not a valid date.',
        ],
        '`start_date` is Y-m-d H:i:s format' => [
            ['enrollments' => [['start_date' => '2020-01-01']]],
            'enrollments.0.start_date',
            'The enrollments.0.start_date does not match the format Y-m-d H:i:s.',
        ],
        '`end_date` is a valid date' => [
            ['enrollments' => [['end_date' => 'not-a-date']]],
            'enrollments.0.end_date',
            'The enrollments.0.end_date is not a valid date.',
        ],
        '`end_date` is Y-m-d H:i:s format' => [
            ['enrollments' => [['end_date' => '2020-01-01']]],
            'enrollments.0.end_date',
            'The enrollments.0.end_date does not match the format Y-m-d H:i:s.',
        ],
    ]);
