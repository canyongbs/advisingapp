<?php

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
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
        'programs' => [StudentProgramRequestFactory::new()->create()],
    ];

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.programs.put', ['student' => $student], false), $createStudentProgramRequestData)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.programs.put', ['student' => $student], false), $createStudentProgramRequestData)
        ->assertForbidden();

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

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('program.*.update');
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.programs.put', ['student' => $student], false), $createStudentProgramRequestData)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('program.*.delete');
    Sanctum::actingAs($user, ['api']);
    putJson(route('api.v1.students.programs.put', ['student' => $student], false), $createStudentProgramRequestData)
        ->assertForbidden();

    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'program.view-any', 'program.create', 'program.*.update', 'program.*.delete']);
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
    $user->givePermissionTo(['student.view-any', 'program.view-any', 'program.create']);
    Sanctum::actingAs($user, ['api']);

    $createStudentProgramRequestData = [
        'programs' => [StudentProgramRequestFactory::new()->create()],
    ];

    $response = putJson(route('api.v1.students.programs.put', ['student' => $student], false), $createStudentProgramRequestData);
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);

    expect(Carbon::parse($response['data'][0]['declare_dt'])->toDateTimeString())
        ->toBe(Carbon::parse($createStudentProgramRequestData['programs'][0]['declare_dt'])->toDateTimeString());

    expect($response['data'][0]['acad_career'])
        ->toBe($createStudentProgramRequestData['programs'][0]['acad_career']);

    expect(json_decode($response['data'][0]['acad_plan'], true))
        ->toBe($createStudentProgramRequestData['programs'][0]['acad_plan']);

    expect($response['data'][0]['division'])
        ->toBe($createStudentProgramRequestData['programs'][0]['division']);

    expect($response['data'][0]['prog_status'])
        ->toBe($createStudentProgramRequestData['programs'][0]['prog_status']);

    expect($response['data'][0]['cum_gpa'])
        ->toBe($createStudentProgramRequestData['programs'][0]['cum_gpa']);

    expect($response['data'][0]['semester'])
        ->toBe($createStudentProgramRequestData['programs'][0]['semester']);

    expect($response['data'][0]['descr'])
        ->toBe($createStudentProgramRequestData['programs'][0]['descr']);

    if (isset($createStudentProgramRequestData['programs'][0]['foi'])) {
        expect($response['data'][0]['foi'])
            ->toBe($createStudentProgramRequestData['programs'][0]['foi']);
    }

    expect(Carbon::parse($response['data'][0]['change_dt'])->toDateTimeString())
        ->toBe(Carbon::parse($createStudentProgramRequestData['programs'][0]['change_dt'])->toDateTimeString());

    assertDatabaseHas('programs', [
        'sisid' => $student->sisid,
        'declare_dt' => Carbon::parse($createStudentProgramRequestData['programs'][0]['declare_dt'])->toDateTimeString(),
    ]);
});

it('validates', function (array $requestAttributes, string $invalidAttribute, string $validationMessage, ?Closure $before = null) {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $student = Student::factory()->create();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'program.view-any', 'program.create']);
    Sanctum::actingAs($user, ['api']);

    $before?->call($this);

    $createStudentProgramRequestData = [
        'programs' => [
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
        '`programs.*.acad_career` max' => [
            ['programs' => [['acad_career' => str_repeat('a', 256)]]],
            'programs.0.acad_career',
            'The programs.0.acad_career may not be greater than 255 characters.',
        ],
        '`programs.*.division` max' => [
            ['programs' => [['division' => str_repeat('a', 256)]]],
            'programs.0.division',
            'The programs.0.division may not be greater than 255 characters.',
        ],
        '`programs.*.prog_status` max' => [
            ['programs' => [['prog_status' => str_repeat('a', 256)]]],
            'programs.0.prog_status',
            'The programs.0.prog_status may not be greater than 255 characters.',
        ],
        '`programs.*.cum_gpa` must have 0-2 decimal places' => [
            ['programs' => [['cum_gpa' => 4.337]]],
            'programs.0.cum_gpa',
            'The programs.0.cum_gpa field must have 0-2 decimal places.',
        ],
        '`programs.*.cum_gpa` must be decimal' => [
            ['programs' => [['cum_gpa' => 'test']]],
            'programs.0.cum_gpa',
            'The programs.0.cum_gpa field must have 0-2 decimal places.',
        ],
        '`programs.*.acad_plan` is required' => [
            ['programs' => [['acad_plan' => null]]],
            'programs.0.acad_plan',
            'The programs.0.acad_plan field is required.',
        ],
        '`programs.*.acad_plan` must be an array' => [
            ['programs' => [['acad_plan' => 'This is simple string']]],
            'programs.0.acad_plan',
            'The programs.0.acad_plan must be an array.',
        ],
        '`programs.*.semester` max' => [
            ['programs' => [['semester' => str_repeat('a', 256)]]],
            'programs.0.semester',
            'The programs.0.semester may not be greater than 255 characters.',
        ],
        '`programs.*.descr` max' => [
            ['programs' => [['descr' => str_repeat('a', 256)]]],
            'programs.0.descr',
            'The programs.0.descr may not be greater than 255 characters.',
        ],
        '`programs.*.foi` max' => [
            ['programs' => [['foi' => str_repeat('a', 256)]]],
            'programs.0.foi',
            'The programs.0.foi may not be greater than 255 characters.',
        ],
        '`programs.*.declare_dt` is required' => [
            ['programs' => [['declare_dt' => null]]],
            'programs.0.declare_dt',
            'The programs.0.declare_dt field is required.',
        ],
        '`declare_dt` is a valid date' => [
            ['programs' => [['declare_dt' => 'not-a-date']]],
            'programs.0.declare_dt',
            'The programs.0.declare_dt is not a valid date.',
        ],
        '`declare_dt` is Y-m-d H:i:s format' => [
            ['programs' => [['declare_dt' => '2020-01-01']]],
            'programs.0.declare_dt',
            'The programs.0.declare_dt does not match the format Y-m-d H:i:s.',
        ],
        '`change_dt` is a valid date' => [
            ['programs' => [['change_dt' => 'not-a-date']]],
            'programs.0.change_dt',
            'The programs.0.change_dt is not a valid date.',
        ],
        '`change_dt` is Y-m-d H:i:s format' => [
            ['programs' => [['change_dt' => '2020-01-01']]],
            'programs.0.change_dt',
            'The programs.0.change_dt does not match the format Y-m-d H:i:s.',
        ],
    ]);
