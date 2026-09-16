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

use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Portal\Enums\PortalType;
use AdvisingApp\Portal\Models\PortalAuthentication;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Support\AuthenticationCodeRateLimiter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;

use function Pest\Laravel\postJson;
use function Pest\Laravel\withHeader;

beforeEach(function () {
    withHeader('Origin', config('app.url'));
});

/**
 * Creates an embed enabled advisor and an authentication holding $code for $student, and
 * returns the signed confirm URL for the pair. The URL is returned rather than posted to
 * so callers can hit the same authentication repeatedly.
 */
function customerAdvisorAuthenticationUrl(Student $student, string $code): string
{
    $advisor = CustomerAdvisor::factory()->create(['is_embed_enabled' => true]);

    $authentication = PortalAuthentication::factory()
        ->state([
            'code' => Hash::make($code),
            'educatable_id' => $student->getKey(),
            'educatable_type' => $student->getMorphClass(),
            'portal_type' => PortalType::CustomerAdvisorWidget,
        ])
        ->create();

    return URL::signedRoute(
        name: 'widgets.ai.customer-advisors.api.authentication.confirm',
        parameters: ['advisor' => $advisor, 'authentication' => $authentication],
    );
}

/**
 * @return TestResponse<JsonResponse>
 */
function confirmCustomerAdvisorAuthentication(Student $student, string $code = '123456'): TestResponse
{
    return postJson(customerAdvisorAuthenticationUrl($student, $code), ['code' => $code]);
}

it('issues tokens for an active student', function () {
    $student = Student::factory()->create();

    confirmCustomerAdvisorAuthentication($student)
        ->assertSuccessful()
        ->assertJsonStructure(['access_token']);
});

// The middleware already rejects an archived student on every functional route, but redeeming
// the code would still hand out a three day refresh cookie and report a successful sign in.
it('rejects a code issued before the student was archived', function () {
    $student = Student::factory()->create();
    $student->archive();

    confirmCustomerAdvisorAuthentication($student)
        ->assertForbidden()
        ->assertJson(['message' => 'Authentication code is expired.']);
});

it('locks out after too many invalid code attempts', function () {
    $code = '123456';

    $url = customerAdvisorAuthenticationUrl(Student::factory()->create(), $code);

    for ($attempt = 0; $attempt < AuthenticationCodeRateLimiter::MAX_ATTEMPTS; $attempt++) {
        postJson($url, ['code' => '654321'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['code' => 'The provided code is invalid.']);
    }

    // Once locked out, even the correct code must be rejected.
    postJson($url, ['code' => $code])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['code' => 'Too many invalid attempts. Please request a new code.']);
});

it('resets the attempt counter after a successful authentication', function () {
    $code = '123456';

    $url = customerAdvisorAuthenticationUrl(Student::factory()->create(), $code);

    // Record one failed attempt so the counter is non-zero before the successful attempt.
    postJson($url, ['code' => '654321'])->assertStatus(422);

    postJson($url, ['code' => $code])->assertSuccessful();

    // If the attempt counter were not reset on success, it would still be sitting at 1
    // from the failed attempt above, and locking out would happen sooner than a full
    // MAX_ATTEMPTS invalid attempts. Reusing the same authentication, drive it through
    // the exact same lockout sequence as the "locks out" test above: this only succeeds
    // if the counter was actually reset to zero.
    for ($attempt = 0; $attempt < AuthenticationCodeRateLimiter::MAX_ATTEMPTS; $attempt++) {
        postJson($url, ['code' => '111111'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['code' => 'The provided code is invalid.']);
    }

    postJson($url, ['code' => '111111'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['code' => 'Too many invalid attempts. Please request a new code.']);
});
