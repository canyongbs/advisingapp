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
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
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
    $student = Student::factory()->create();
    $createStudentEmailAddressRequestData = CreateStudentEmailAddressRequestFactory::new()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.email-addresses.create', ['student' => $student], false), $createStudentEmailAddressRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.view-any');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.email-addresses.create', ['student' => $student], false), $createStudentEmailAddressRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('student.*.update');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.email-addresses.create', ['student' => $student], false), $createStudentEmailAddressRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.email-addresses.create', ['student' => $student], false), $createStudentEmailAddressRequestData);

    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.students.email-addresses.create', ['student' => $student], false), $createStudentEmailAddressRequestData)
        ->assertCreated();
});

it('creates a student email address', function () {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);

    $student = Student::factory()->create();
    $createStudentEmailAddressRequestData = CreateStudentEmailAddressRequestFactory::new()->create();

    $response = postJson(route('api.v1.students.email-addresses.create', ['student' => $student], false), $createStudentEmailAddressRequestData);
    $response->assertCreated();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data']['sisid'] ?? null)
        ->toBe($student->sisid);

    expect($response['data']['address'] ?? null)
        ->toBe($createStudentEmailAddressRequestData['address']);

    if (isset($createStudentEmailAddressRequestData['type'])) {
        expect($response['data']['type'] ?? null)
            ->toBe($createStudentEmailAddressRequestData['type']);
    }

    if (isset($createStudentEmailAddressRequestData['order'])) {
        expect($response['data']['order'] ?? null)
            ->toBe($createStudentEmailAddressRequestData['order']);
    }

    assertDatabaseHas(StudentEmailAddress::class, [
        'sisid' => $student->sisid,
        'address' => $createStudentEmailAddressRequestData['address'],
    ]);
});

it('validates', function (array $requestAttributes, string $invalidAttribute, string $validationMessage, ?Closure $before = null) {
    $studentConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    $studentConfigurationSettings->is_enabled = true;
    $studentConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['student.view-any', 'student.*.update']);
    Sanctum::actingAs($user, ['api']);

    $before?->call($this);

    $student = Student::factory()->create();
    $createStudentEmailAddressRequestData = CreateStudentEmailAddressRequestFactory::new()->create($requestAttributes);

    $response = postJson(route('api.v1.students.email-addresses.create', ['student' => $student], false), $createStudentEmailAddressRequestData);
    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        $invalidAttribute => [$validationMessage],
    ]);
})->with([
    // requestAttributes, invalidAttribute, validationMessage, before
    '`address` is required' => [['address' => null], 'address', 'The address field is required.'],
    '`address` is a valid email' => [['address' => 'not-an-email'], 'address', 'The address must be a valid email address.'],
    '`type` is max 255 characters' => [['type' => str_repeat('a', 256)], 'type', 'The type may not be greater than 255 characters.'],
    '`order` is integer' => [['order' => 'not-an-integer'], 'order', 'The order must be an integer.'],
]);
