<?php

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use AdvisingApp\StudentDataModel\Tests\Tenant\Http\Controllers\Api\V1\Students\RequestFactories\CreateStudentRequestFactory;
use AdvisingApp\StudentDataModel\Tests\Tenant\Http\Controllers\Api\V1\Students\StudentEmailAddresses\RequestFactories\CreateStudentEmailAddressRequestFactory;
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

    expect($response['data']['sisid'] ?? null)
        ->toBe($createStudentRequestData['sisid']);

    if (isset($createStudentRequestData['otherid'])) {
        expect($response['data']['otherid'] ?? null)
            ->toBe($createStudentRequestData['otherid']);
    }

    expect($response['data']['first'] ?? null)
        ->toBe($createStudentRequestData['first']);

    expect($response['data']['last'] ?? null)
        ->toBe($createStudentRequestData['last']);

    expect($response['data']['full_name'] ?? null)
        ->toBe($createStudentRequestData['full_name']);

    if (isset($createStudentRequestData['preferred'])) {
        expect($response['data']['preferred'] ?? null)
            ->toBe($createStudentRequestData['preferred']);
    }

    if (isset($createStudentRequestData['birthdate'])) {
        expect($response['data']['birthdate'] ?? null)
            ->toBe($createStudentRequestData['birthdate']);
    }

    if (isset($createStudentRequestData['hsgrad'])) {
        expect($response['data']['hsgrad'] ?? null)
            ->toBe($createStudentRequestData['hsgrad']);
    }

    if (isset($createStudentRequestData['gender'])) {
        expect($response['data']['gender'] ?? null)
            ->toBe($createStudentRequestData['gender']);
    }

    if (isset($createStudentRequestData['sms_opt_out'])) {
        expect($response['data']['sms_opt_out'] ?? null)
            ->toBe($createStudentRequestData['sms_opt_out']);
    }

    if (isset($createStudentRequestData['email_bounce'])) {
        expect($response['data']['email_bounce'] ?? null)
            ->toBe($createStudentRequestData['email_bounce']);
    }

    if (isset($createStudentRequestData['dual'])) {
        expect($response['data']['dual'] ?? null)
            ->toBe($createStudentRequestData['dual']);
    }

    if (isset($createStudentRequestData['ferpa'])) {
        expect($response['data']['ferpa'] ?? null)
            ->toBe($createStudentRequestData['ferpa']);
    }

    if (isset($createStudentRequestData['firstgen'])) {
        expect($response['data']['firstgen'] ?? null)
            ->toBe($createStudentRequestData['firstgen']);
    }

    if (isset($createStudentRequestData['sap'])) {
        expect($response['data']['sap'] ?? null)
            ->toBe($createStudentRequestData['sap']);
    }

    if (isset($createStudentRequestData['holds'])) {
        expect($response['data']['holds'] ?? null)
            ->toBe($createStudentRequestData['holds']);
    }

    if (isset($createStudentRequestData['dfw'])) {
        expect($response['data']['dfw'] ?? null)
            ->toBe($createStudentRequestData['dfw']);
    }

    if (isset($createStudentRequestData['ethnicity'])) {
        expect($response['data']['ethnicity'] ?? null)
            ->toBe($createStudentRequestData['ethnicity']);
    }

    if (isset($createStudentRequestData['lastlmslogin'])) {
        expect($response['data']['lastlmslogin'] ?? null)
            ->toBe($createStudentRequestData['lastlmslogin']);
    }

    if (isset($createStudentRequestData['f_e_term'])) {
        expect($response['data']['f_e_term'] ?? null)
            ->toBe($createStudentRequestData['f_e_term']);
    }

    if (isset($createStudentRequestData['mr_e_term'])) {
        expect($response['data']['mr_e_term'] ?? null)
            ->toBe($createStudentRequestData['mr_e_term']);
    }

    assertDatabaseHas(Student::class, [
        'sisid' => $createStudentRequestData['sisid'],
    ]);
});

it('creates student email addresses', function () {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.create']);
    Sanctum::actingAs($user, ['api']);

    $createStudentRequestData = CreateStudentRequestFactory::new()->create([
        'email_addresses' => [
            CreateStudentEmailAddressRequestFactory::new()->create(),
            CreateStudentEmailAddressRequestFactory::new()->create(),
        ],
    ]);

    $response = postJson(route('api.v1.students.create', ['include' => 'email_addresses,primary_email_address'], false), $createStudentRequestData);
    $response->assertCreated();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data']['email_addresses'] ?? null)
        ->toBeArray()
        ->toHaveCount(2);

    expect($response['data']['email_addresses'][0]['address'] ?? null)
        ->toBe($createStudentRequestData['email_addresses'][0]['address']);

    if (isset($createStudentRequestData['email_addresses'][0]['type'])) {
        expect($response['data']['email_addresses'][0]['type'] ?? null)
            ->toBe($createStudentRequestData['email_addresses'][0]['type']);
    }

    expect($response['data']['email_addresses'][1]['address'] ?? null)
        ->toBe($createStudentRequestData['email_addresses'][1]['address']);

    if (isset($createStudentRequestData['email_addresses'][1]['type'])) {
        expect($response['data']['email_addresses'][1]['type'] ?? null)
            ->toBe($createStudentRequestData['email_addresses'][1]['type']);
    }

    expect($response['data']['primary_email_id'])
        ->toBe($response['data']['email_addresses'][0]['id']);

    expect($response['data']['primary_email_address'] ?? null)
        ->toBeArray();

    expect($response['data']['primary_email_address']['address'] ?? null)
        ->toBe($response['data']['email_addresses'][0]['address']);

    if (isset($createStudentRequestData['email_addresses'][0]['type'])) {
        expect($response['data']['primary_email_address']['type'] ?? null)
            ->toBe($createStudentRequestData['email_addresses'][0]['type']);
    }
});

it('validates', function (array $requestAttributes, string $invalidAttribute, string $validationMessage, ?Closure $before = null) {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.create']);
    Sanctum::actingAs($user, ['api']);

    $before?->call($this);

    $createStudentRequestData = CreateStudentRequestFactory::new()->create([
        'email_addresses' => [
            CreateStudentEmailAddressRequestFactory::new()->create(),
        ],
        ...$requestAttributes,
    ]);

    $response = postJson(route('api.v1.students.create', [], false), $createStudentRequestData);
    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        $invalidAttribute => [$validationMessage],
    ]);
})->with([
    // requestAttributes, invalidAttribute, validationMessage, before
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
    '`birthdate` is a valid date' => [['birthdate' => 'not-a-date'], 'birthdate', 'The birthdate is not a valid date.'],
    '`birthdate` is Y-m-d format' => [['birthdate' => '2020/01/01'], 'birthdate', 'The birthdate does not match the format Y-m-d.'],
    '`hsgrad` is numeric' => [['hsgrad' => 'not-a-number'], 'hsgrad', 'The hsgrad must be a number.'],
    '`gender` is max 255 characters' => [['gender' => str_repeat('a', 256)], 'gender', 'The gender may not be greater than 255 characters.'],
    '`sms_opt_out` is boolean' => [['sms_opt_out' => 'not-boolean'], 'sms_opt_out', 'The sms opt out field must be true or false.'],
    '`email_bounce` is boolean' => [['email_bounce' => 'not-boolean'], 'email_bounce', 'The email bounce field must be true or false.'],
    '`dual` is boolean' => [['dual' => 'not-boolean'], 'dual', 'The dual field must be true or false.'],
    '`ferpa` is boolean' => [['ferpa' => 'not-boolean'], 'ferpa', 'The ferpa field must be true or false.'],
    '`firstgen` is boolean' => [['firstgen' => 'not-boolean'], 'firstgen', 'The firstgen field must be true or false.'],
    '`sap` is boolean' => [['sap' => 'not-boolean'], 'sap', 'The sap field must be true or false.'],
    '`holds` is max 255 characters' => [['holds' => str_repeat('a', 256)], 'holds', 'The holds may not be greater than 255 characters.'],
    '`dfw` is a valid date' => [['dfw' => 'not-a-date'], 'dfw', 'The dfw is not a valid date.'],
    '`dfw` is Y-m-d format' => [['dfw' => '2020/01/01'], 'dfw', 'The dfw does not match the format Y-m-d.'],
    '`ethnicity` is max 255 characters' => [['ethnicity' => str_repeat('a', 256)], 'ethnicity', 'The ethnicity may not be greater than 255 characters.'],
    '`lastlmslogin` is a valid date' => [['lastlmslogin' => 'not-a-date'], 'lastlmslogin', 'The lastlmslogin is not a valid date.'],
    '`lastlmslogin` is Y-m-d H:i:s format' => [['lastlmslogin' => '2020-01-01'], 'lastlmslogin', 'The lastlmslogin does not match the format Y-m-d H:i:s.'],
    '`f_e_term` is max 255 characters' => [['f_e_term' => str_repeat('a', 256)], 'f_e_term', 'The f e term may not be greater than 255 characters.'],
    '`mr_e_term` is max 255 characters' => [['mr_e_term' => str_repeat('a', 256)], 'mr_e_term', 'The mr e term may not be greater than 255 characters.'],
    '`email_addresses` is an array' => [['email_addresses' => 'not-an-array'], 'email_addresses', 'The email addresses must be an array.'],
    '`email_addresses.*` is an array' => [['email_addresses' => ['not-an-array']], 'email_addresses.0', 'The email_addresses.0 must be an array.'],
    '`email_addresses.*.address` is required' => [['email_addresses' => [['address' => null]]], 'email_addresses.0.address', 'The email_addresses.0.address field is required.'],
    '`email_addresses.*.address` is a valid email' => [['email_addresses' => [['address' => 'not-an-email']]], 'email_addresses.0.address', 'The email_addresses.0.address must be a valid email address.'],
    '`email_addresses.*.type` is max 255 characters' => [['email_addresses' => [['address' => 'test@example.com', 'type' => str_repeat('a', 256)]]], 'email_addresses.0.type', 'The email_addresses.0.type may not be greater than 255 characters.'],
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
