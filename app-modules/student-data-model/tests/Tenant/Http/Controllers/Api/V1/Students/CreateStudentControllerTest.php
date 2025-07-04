<?php

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use AdvisingApp\StudentDataModel\Tests\Tenant\Http\Controllers\Api\V1\Students\RequestFactories\CreateStudentRequestFactory;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

beforeEach(function () {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $createStudentRequestData = CreateStudentRequestFactory::new()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.create', [], false), $createStudentRequestData)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.create', [], false), $createStudentRequestData)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.create');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.create', [], false), $createStudentRequestData)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.create']);
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.create', [], false), $createStudentRequestData)
        ->assertForbidden();

    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.create']);
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.create', [], false), $createStudentRequestData)
        ->assertCreated();
});

it('creates a student', function () {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.create']);
    Sanctum::actingAs($user, ['api']);

    $createStudentRequestData = CreateStudentRequestFactory::new()->create();

    $response = postJson(route('api.v1.students.create', [], false), $createStudentRequestData);
    $response->assertCreated();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data']['sisid'])
        ->toBe($createStudentRequestData['sisid']);

    assertDatabaseHas(Student::class, [
        'sisid' => $createStudentRequestData['sisid'],
    ]);
});

it('validates', function (array $requestAttributes, string $invalidAttribute, string $validationMessage, ?Closure $before = null) {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.create']);
    Sanctum::actingAs($user, ['api']);

    $before?->call($this);

    $createStudentRequestData = CreateStudentRequestFactory::new()->create($requestAttributes);

    $response = postJson(route('api.v1.students.create', [], false), $createStudentRequestData);
    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        $invalidAttribute => [$validationMessage],
    ]);
})->with([
    '`sisid` is required' => [['sisid' => null], 'sisid', 'The sisid field is required.'],
    '`sisid` is max 255 characters' => [['sisid' => str_repeat('a', 256)], 'sisid', 'The sisid may not be greater than 255 characters.'],
    '`sisid` is alpha dash' => [['sisid' => 'invalid sisid!'], 'sisid', 'The sisid may only contain letters, numbers, and dashes.'],
    '`sisid` is unique' => [['sisid' => 'existing-sisid'], 'sisid', 'The sisid has already been taken.', function () {
        Student::factory()->create(['sisid' => 'existing-sisid']);
    }],
    '`otherid` is max 255 characters' => [['otherid' => str_repeat('a', 256)], 'otherid', 'The otherid may not be greater than 255 characters.'],
    '`first` is required' => [['first' => null], 'first', 'The first field is required.'],
    '`first` is max 255 characters' => [['first' => str_repeat('a', 256)], 'first', 'The first may not be greater than 255 characters.'],
    '`last` is required' => [['last' => null], 'last', 'The last field is required.'],
    '`last` is max 255 characters' => [['last' => str_repeat('a', 256)], 'last', 'The last may not be greater than 255 characters.'],
    '`full_name` is required' => [['full_name' => null], 'full_name', 'The full name field is required.'],
    '`full_name` is max 255 characters' => [['full_name' => str_repeat('a', 256)], 'full_name', 'The full name may not be greater than 255 characters.'],
    '`preferred` is max 255 characters' => [['preferred' => str_repeat('a', 256)], 'preferred', 'The preferred may not be greater than 255 characters.'],
]);

it('can include related student relationships', function (string $relationship, string $responseKey) {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.create']);
    Sanctum::actingAs($user, ['api']);

    $createStudentRequestData = CreateStudentRequestFactory::new()->create();

    $response = postJson(route('api.v1.students.create', [], false), $createStudentRequestData);
    $response->assertCreated();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data'])
        ->not()->toHaveKey($responseKey);

    $createStudentRequestData = CreateStudentRequestFactory::new()->create();

    $response = postJson(route('api.v1.students.create', ['include' => $relationship], false), $createStudentRequestData);
    $response->assertCreated();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data'])
        ->toHaveKey($responseKey);
})->with([
    // relationship, responseKey
    '`emailAddresses`' => ['email_addresses', 'email_addresses'],
    '`primaryEmailAddress`' => ['primary_email_address', 'primary_email_address'],
]);
