<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Portal\Enums\PortalType;
use AdvisingApp\Portal\Http\Controllers\ResourceHub\ResourceHubPortalAuthenticateController;
use AdvisingApp\Portal\Models\PortalAuthentication;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * @return array<string, mixed>
 */
function authenticateResourceHubPortal(Student $student, string $code = '123456'): array
{
    $authentication = PortalAuthentication::factory()->create([
        'code' => Hash::make($code),
        'educatable_id' => $student->getKey(),
        'educatable_type' => $student->getMorphClass(),
        'portal_type' => PortalType::ResourceHub,
    ]);

    $response = app(ResourceHubPortalAuthenticateController::class)(
        Request::create('/', 'POST', ['code' => $code]),
        $authentication,
    );

    return json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
}

it('authenticates a student and issues a token', function () {
    $student = Student::factory()->create();

    $payload = authenticateResourceHubPortal($student);

    expect($payload['success'] ?? null)->toBeTrue()
        ->and($payload['token'] ?? null)->toBeString()
        ->and(Auth::guard('student')->id())->toBe($student->getKey());
});

// A code is valid for a day, so a student can be archived between requesting and redeeming it.
it('does not authenticate an archived student', function () {
    $student = Student::factory()->create();
    $student->archive();

    $payload = authenticateResourceHubPortal($student);

    expect($payload['is_expired'] ?? null)->toBeTrue()
        ->and($payload)->not->toHaveKey('token')
        ->and(Auth::guard('student')->check())->toBeFalse();
});
