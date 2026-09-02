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

use AdvisingApp\Ai\Http\Controllers\CustomerAdvisors\AuthenticationConfirmController;
use AdvisingApp\Ai\Http\Requests\CustomerAdvisors\AuthenticationConfirmRequest;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Portal\Enums\PortalType;
use AdvisingApp\Portal\Models\PortalAuthentication;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;

function confirmCustomerAdvisorAuthentication(Student $student, string $code = '123456'): JsonResponse
{
    $advisor = CustomerAdvisor::factory()->create();

    $authentication = PortalAuthentication::factory()
        ->state([
            'code' => Hash::make($code),
            'educatable_id' => $student->getKey(),
            'educatable_type' => $student->getMorphClass(),
            'portal_type' => PortalType::CustomerAdvisorWidget,
        ])
        ->create();

    $request = AuthenticationConfirmRequest::create('/', 'POST', ['code' => $code]);
    $request->setContainer(app());
    $request->validateResolved();

    return app(AuthenticationConfirmController::class)($request, $advisor, $authentication);
}

it('issues tokens for an active student', function () {
    $student = Student::factory()->create();

    $payload = json_decode(
        confirmCustomerAdvisorAuthentication($student)->getContent(),
        true,
        512,
        JSON_THROW_ON_ERROR,
    );

    expect($payload['access_token'] ?? null)->toBeString();
});

// The middleware already rejects an archived student on every functional route, but redeeming
// the code would still hand out a three day refresh cookie and report a successful sign in.
it('rejects a code issued before the student was archived', function () {
    $student = Student::factory()->create();
    $student->archive();

    expect(fn (): JsonResponse => confirmCustomerAdvisorAuthentication($student))
        ->toThrow(HttpException::class, 'Authentication code is expired.');
});
