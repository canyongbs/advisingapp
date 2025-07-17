<?php

use AdvisingApp\StudentDataModel\Models\Program;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use AdvisingApp\StudentDataModel\Tests\Tenant\Http\Controllers\Api\V1\Students\RequestFactories\CreateStudentRequestFactory;
use AdvisingApp\StudentDataModel\Tests\Tenant\Http\Controllers\Api\V1\Students\StudentPrograms\RequestFactories\StudentProgramRequestFactory;
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

    $createStudentProgramRequestData = [
        'program' => [StudentProgramRequestFactory::new()->create()],
    ];

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.programs.put', ['student' => $student], false), $createStudentProgramRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.programs.put', ['student' => $student], false), $createStudentProgramRequestData)
    ->assertForbidden();;

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('program.view-any');
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.programs.put', ['student' => $student], false), $createStudentProgramRequestData)
    ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('program.create');
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.programs.put', ['student' => $student], false), $createStudentProgramRequestData)
    ->assertForbidden();

    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any','program.view-any','program.create']);
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.programs.put', ['student' => $student], false), $createStudentProgramRequestData)
        ->assertOk();
});

it('creates a student program', function () {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $student = Student::factory()->create();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any','program.view-any','program.create']);
    Sanctum::actingAs($user, ['api']);

    $createStudentProgramRequestData = [
        'program' => [StudentProgramRequestFactory::new()->create()],
    ];

    $response = putJson(route('api.v1.students.programs.put', ['student' => $student], false), $createStudentProgramRequestData);
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);

    expect(Carbon::parse($response['data'][0]['declare_dt'])->toDateTimeString())
        ->toBe(Carbon::parse($createStudentProgramRequestData['program'][0]['declare_dt'])->toDateTimeString());

    expect($response['data'][0]['acad_career'])
        ->toBe($createStudentProgramRequestData['program'][0]['acad_career']);

    expect($response['data'][0]['acad_plan'])
        ->toBe($createStudentProgramRequestData['program'][0]['acad_plan']);

    expect($response['data'][0]['division'])
        ->toBe($createStudentProgramRequestData['program'][0]['division']);

    expect($response['data'][0]['prog_status'])
        ->toBe($createStudentProgramRequestData['program'][0]['prog_status']);
        
    expect($response['data'][0]['cum_gpa'])
        ->toBe($createStudentProgramRequestData['program'][0]['cum_gpa']);

    expect($response['data'][0]['semester'])
        ->toBe($createStudentProgramRequestData['program'][0]['semester']);

    expect($response['data'][0]['descr'])
        ->toBe($createStudentProgramRequestData['program'][0]['descr']);

    if (isset($createStudentProgramRequestData['program'][0]['foi'])) {
        expect($response['data'][0]['foi'])
            ->toBe($createStudentProgramRequestData['program'][0]['foi']);
    }

    expect(Carbon::parse($response['data'][0]['change_dt'])->toDateTimeString())
        ->toBe(Carbon::parse($createStudentProgramRequestData['program'][0]['change_dt'])->toDateTimeString());

    assertDatabaseHas('programs', [
        'sisid' => $student->sisid,
        'declare_dt' => Carbon::parse($createStudentProgramRequestData['program'][0]['declare_dt'])->toDateTimeString(),
    ]);
});

it('validates', function (array $requestAttributes, string $invalidAttribute, string $validationMessage, ?Closure $before = null) {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $student = Student::factory()->create();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any','program.view-any','program.create']);
    Sanctum::actingAs($user, ['api']);

    $before?->call($this);

    $createStudentProgramRequestData = [
        'program' => [
            StudentProgramRequestFactory::new()->create(),
        ],
        ...$requestAttributes,
    ];

    $response = putJson(route('api.v1.students.programs.put', ['student' => $student], false), $createStudentProgramRequestData);
    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        $invalidAttribute => [$validationMessage],
    ]);
})
->with([
    '`program.*.acad_career` max' => [
        ['program' => [['acad_career' => str_repeat('a', 256)]]],
        'program.0.acad_career',
        'The program.0.acad_career may not be greater than 255 characters.'
    ],
    '`program.*.division` max' => [
        ['program' => [['division' => str_repeat('a', 256)]]],
        'program.0.division',
        'The program.0.division may not be greater than 255 characters.'
    ],
    '`program.*.prog_status` max' => [
        ['program' => [['prog_status' => str_repeat('a', 256)]]],
        'program.0.prog_status',
        'The program.0.prog_status may not be greater than 255 characters.'
    ],
    '`program.*.cum_gpa` must have 0-2 decimal places' => [
        ['program' => [['cum_gpa' => 4.337]]],
        'program.0.cum_gpa',
        'The program.0.cum_gpa field must have 0-2 decimal places.'
    ],
    '`program.*.cum_gpa` must be decimal' => [
        ['program' => [['cum_gpa' => 'test']]],
        'program.0.cum_gpa',
        'The program.0.cum_gpa field must have 0-2 decimal places.'
    ],
    '`program.*.acad_plan` is required' => [
        ['program' => [['acad_plan' => null]]],
        'program.0.acad_plan',
        'The program.0.acad_plan field is required.'
    ],
    '`program.*.semester` max' => [
        ['program' => [['semester' => str_repeat('a', 256)]]],
        'program.0.semester',
        'The program.0.semester may not be greater than 255 characters.'
    ],
    '`program.*.descr` max' => [
        ['program' => [['descr' => str_repeat('a', 256)]]],
        'program.0.descr',
        'The program.0.descr may not be greater than 255 characters.'
    ],
    '`program.*.foi` max' => [
        ['program' => [['foi' => str_repeat('a', 256)]]],
        'program.0.foi',
        'The program.0.foi may not be greater than 255 characters.'
    ],
    '`program.*.declare_dt` is required' => [
        ['program' => [['declare_dt' => null]]],
        'program.0.declare_dt',
        'The program.0.declare_dt field is required.'
    ],
    '`declare_dt` is a valid date' => [
        ['program' => [['declare_dt' => 'not-a-date']]],
        'program.0.declare_dt',
        'The program.0.declare_dt is not a valid date.'
    ],
    '`declare_dt` is Y-m-d H:i:s format' => [
        ['program' => [['declare_dt' => '2020-01-01']]],
        'program.0.declare_dt',
        'The program.0.declare_dt does not match the format Y-m-d H:i:s.'
    ],
    '`change_dt` is a valid date' => [
        ['program' => [['change_dt' => 'not-a-date']]],
        'program.0.change_dt',
        'The program.0.change_dt is not a valid date.'
    ],
    '`change_dt` is Y-m-d H:i:s format' => [
        ['program' => [['change_dt' => '2020-01-01']]],
        'program.0.change_dt',
        'The program.0.change_dt does not match the format Y-m-d H:i:s.'
    ],
]);
