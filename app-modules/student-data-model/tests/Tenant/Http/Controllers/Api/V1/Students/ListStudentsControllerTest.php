<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

beforeEach(function () {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.students.index', [], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.students.index', [], false))
        ->assertOk();
});

it('returns a paginated list of students', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);

    Student::factory()->count(3)->create();

    $response = getJson(route('api.v1.students.index', [], false));
    $response->assertOk();
    $response->assertJsonStructure([
        'data', 'links', 'meta',
    ]);

    expect($response['data'])
        ->toHaveCount(3);
});

it('can filter students by all attributes', function (string $requestKey, mixed $requestValue, array $includedAttributes, array $excludedAttributes, string $responseKey, mixed $responseValue) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);

    Student::factory()->create($includedAttributes);
    // Seed two students with the same non-matching attributes
    Student::factory()->create($excludedAttributes);
    Student::factory()->create($excludedAttributes);

    $response = getJson(route('api.v1.students.index', ['filter' => [$requestKey => $requestValue]], false));
    $response->assertOk();

    expect($response['data'][0][$responseKey])
        ->toBe($responseValue);
    expect($response['meta']['total'])
        ->toBe(1);
})->with([
    // requestKey, requestValue, includedAttributes, excludedAttributes, responseKey, responseValue
    '`sisid`' => ['sisid', 'ABC123', ['sisid' => 'ABC123'], [], 'sisid', 'ABC123'],
    '`otherid`' => ['otherid', 'XYZ789', ['otherid' => 'XYZ789'], ['otherid' => 'UNIQUE_OTHERID'], 'otherid', 'XYZ789'],
    '`first`' => ['first', 'Alice', ['first' => 'Alice'], ['first' => 'UniqueFirst'], 'first', 'Alice'],
    '`last`' => ['last', 'Smith', ['last' => 'Smith'], ['last' => 'UniqueLast'], 'last', 'Smith'],
    '`full_name`' => ['full_name', 'John Doe', ['full_name' => 'John Doe'], ['full_name' => 'Unique Name'], 'full_name', 'John Doe'],
    '`preferred`' => ['preferred', 'JD', ['preferred' => 'JD'], ['preferred' => 'UniquePref'], 'preferred', 'JD'],
    '`gender`' => ['gender', 'F', ['gender' => 'F'], ['gender' => 'X'], 'gender', 'F'],
    '`ethnicity`' => ['ethnicity', 'Hispanic', ['ethnicity' => 'Hispanic'], ['ethnicity' => 'UniqueEthnicity'], 'ethnicity', 'Hispanic'],
    '`birthdate`' => ['birthdate', '2000-01-01', ['birthdate' => '2000-01-01'], ['birthdate' => '1990-01-01'], 'birthdate', '2000-01-01'],
    '`dfw`' => ['dfw', '2024-05-01', ['dfw' => '2024-05-01'], ['dfw' => '1999-01-01'], 'dfw', '2024-05-01'],
    '`lastlmslogin`' => ['lastlmslogin', '2025-07-01 00:00:00', ['lastlmslogin' => '2025-07-01 00:00:00'], ['lastlmslogin' => '1999-01-01 00:00:00'], 'lastlmslogin', '2025-07-01 00:00:00'],
    '`created_at_source`' => ['created_at_source', '2025-01-01 12:00:00', ['created_at_source' => '2025-01-01 12:00:00'], ['created_at_source' => '1999-01-01 00:00:00'], 'created_at_source', '2025-01-01T12:00:00.000000Z'],
    '`updated_at_source`' => ['updated_at_source', '2025-01-02 12:00:00', ['updated_at_source' => '2025-01-02 12:00:00'], ['updated_at_source' => '1999-01-01 00:00:00'], 'updated_at_source', '2025-01-02T12:00:00.000000Z'],
    '`hsgrad`' => ['hsgrad', '2022-01-01', ['hsgrad' => '2022-01-01'], ['hsgrad' => '1999-01-01'], 'hsgrad', '2022-01-01'],
    '`holds`' => ['holds', '1', ['holds' => '1'], ['holds' => '99'], 'holds', '1'],
    '`dual`' => ['dual', true, ['dual' => true], ['dual' => false], 'dual', true],
    '`ferpa`' => ['ferpa', true, ['ferpa' => true], ['ferpa' => false], 'ferpa', true],
    '`sap`' => ['sap', true, ['sap' => true], ['sap' => false], 'sap', true],
    '`firstgen`' => ['firstgen', true, ['firstgen' => true], ['firstgen' => false], 'firstgen', true],
]);

dataset('sorts', [
    // requestKey, firstAttributes, secondAttributes, responseKey, responseFirstValue, responseSecondValue
    '`sisid`' => ['sisid', ['sisid' => 'A'], ['sisid' => 'B'], 'sisid', 'A', 'B'],
    '`otherid`' => ['otherid', ['otherid' => 'A'], ['otherid' => 'B'], 'otherid', 'A', 'B'],
    '`first`' => ['first', ['first' => 'Alice'], ['first' => 'Bob'], 'first', 'Alice', 'Bob'],
    '`last`' => ['last', ['last' => 'Alpha'], ['last' => 'Zulu'], 'last', 'Alpha', 'Zulu'],
    '`full_name`' => ['full_name', ['full_name' => 'A'], ['full_name' => 'B'], 'full_name', 'A', 'B'],
    '`preferred`' => ['preferred', ['preferred' => 'A'], ['preferred' => 'B'], 'preferred', 'A', 'B'],
    '`gender`' => ['gender', ['gender' => 'F'], ['gender' => 'M'], 'gender', 'F', 'M'],
    '`ethnicity`' => ['ethnicity', ['ethnicity' => 'A'], ['ethnicity' => 'B'], 'ethnicity', 'A', 'B'],
    '`birthdate`' => ['birthdate', ['birthdate' => '2000-01-01'], ['birthdate' => '2001-01-01'], 'birthdate', '2000-01-01', '2001-01-01'],
    '`dfw`' => ['dfw', ['dfw' => '2024-05-01'], ['dfw' => '2025-05-01'], 'dfw', '2024-05-01', '2025-05-01'],
    '`lastlmslogin`' => ['lastlmslogin', ['lastlmslogin' => '2025-07-01 00:00:00'], ['lastlmslogin' => '2025-07-02 00:00:00'], 'lastlmslogin', '2025-07-01 00:00:00', '2025-07-02 00:00:00'],
    '`created_at_source`' => ['created_at_source', ['created_at_source' => '2025-01-01 12:00:00'], ['created_at_source' => '2025-01-02 12:00:00'], 'created_at_source', '2025-01-01T12:00:00.000000Z', '2025-01-02T12:00:00.000000Z'],
    '`updated_at_source`' => ['updated_at_source', ['updated_at_source' => '2025-01-01 12:00:00'], ['updated_at_source' => '2025-01-02 12:00:00'], 'updated_at_source', '2025-01-01T12:00:00.000000Z', '2025-01-02T12:00:00.000000Z'],
    '`hsgrad`' => ['hsgrad', ['hsgrad' => '2000-01-01'], ['hsgrad' => '2023-01-01'], 'hsgrad', '2000-01-01', '2023-01-01'],
    '`holds`' => ['holds', ['holds' => '1'], ['holds' => '2'], 'holds', '1', '2'],
]);

it('can sort students by all attributes ascending', function (string $requestKey, array $firstAttributes, array $secondAttributes, string $responseKey, mixed $responseFirstValue, mixed $responseSecondValue) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);

    Student::factory()->create($firstAttributes);
    Student::factory()->create($secondAttributes);

    $response = getJson(route('api.v1.students.index', ['sort' => $requestKey], false));
    $response->assertOk();

    expect($response['data'][0][$responseKey])
        ->toBe($responseFirstValue);
    expect($response['data'][1][$responseKey])
        ->toBe($responseSecondValue);
})->with('sorts');

it('can sort students by all attributes descending', function (string $requestKey, array $firstAttributes, array $secondAttributes, string $responseKey, mixed $responseFirstValue, mixed $responseSecondValue) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);

    Student::factory()->create($firstAttributes);
    Student::factory()->create($secondAttributes);

    $response = getJson(route('api.v1.students.index', ['sort' => '-' . $requestKey], false));
    $response->assertOk();

    expect($response['data'][0][$responseKey])
        ->toBe($responseSecondValue);
    expect($response['data'][1][$responseKey])
        ->toBe($responseFirstValue);
})->with('sorts');

it('can include related student relationships', function (string $relationship, string $responseKey) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);

    Student::factory()->create();

    $response = getJson(route('api.v1.students.index', [], false));
    $response->assertOk();

    expect($response['data'][0])
        ->not()->toHaveKey($responseKey);

    $response = getJson(route('api.v1.students.index', ['include' => $relationship], false));
    $response->assertOk();

    expect($response['data'][0])
        ->toHaveKey($responseKey);
})->with([
    // relationship, responseKey
    '`emailAddresses`' => ['email_addresses', 'email_addresses'],
    '`primaryEmailAddress`' => ['primary_email_address', 'primary_email_address'],
    '`phoneNumbers`' => ['phone_numbers', 'phone_numbers'],
    '`primaryPhoneNumber`' => ['primary_phone_number', 'primary_phone_number'],
    '`firstEnrollmentTerm`' => ['first_enrollment_term', 'first_enrollment_term'],
    '`mostRecentEnrollmentTerm`' => ['most_recent_enrollment_term', 'most_recent_enrollment_term'],
]);
