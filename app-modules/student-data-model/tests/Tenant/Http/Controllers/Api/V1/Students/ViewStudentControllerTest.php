<?php

use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

beforeEach(function () {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $student = Student::factory()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.students.view', ['student' => $student], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.students.view', ['student' => $student], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.*.view');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.students.view', ['student' => $student], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.view']);
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.students.view', ['student' => $student], false))
        ->assertOk();
});

it('returns a student resource', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.view']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();

    $response = getJson(route('api.v1.students.view', ['student' => $student], false));
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data']['sisid'])
        ->toBe($student->sisid);
});

it('can include related student relationships', function (string $relationship, string $responseKey) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.view']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();

    $response = getJson(route('api.v1.students.view', ['student' => $student], false));
    $response->assertOk();

    expect($response['data'])
        ->not()->toHaveKey($responseKey);

    $response = getJson(route('api.v1.students.view', [$student, 'include' => $relationship], false));
    $response->assertOk();

    expect($response['data'])
        ->toHaveKey($responseKey);
})->with([
    // relationship, responseKey
    '`emailAddresses`' => ['email_addresses', 'email_addresses'],
    '`primaryEmailAddress`' => ['primary_email_address', 'primary_email_address'],
]);
