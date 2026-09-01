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
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use App\Support\AuthenticationCodeRateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\postJson;

/** @phpstan-ignore-next-line */
beforeEach(function () {
    /** @phpstan-ignore-next-line */
    $this->advisor = CustomerAdvisor::factory()->create(['is_embed_enabled' => true]);

    /** @phpstan-ignore-next-line */
    $this->withHeader('Origin', config('app.url'));
});

it('throttles repeated code requests for the same target', function () {
    $student = Student::factory()->create();
    $email = 'throttle-target@example.com';

    StudentEmailAddress::factory()->create(['sisid' => $student->sisid, 'address' => $email]);

    postJson(URL::signedRoute(
        name: 'widgets.ai.customer-advisors.api.authentication.request',
        /** @phpstan-ignore-next-line */
        parameters: ['advisor' => $this->advisor],
    ), ['email' => $email])
        ->assertSuccessful();

    // Attempting a second request immediately should be throttled.
    postJson(URL::signedRoute(
        name: 'widgets.ai.customer-advisors.api.authentication.request',
        /** @phpstan-ignore-next-line */
        parameters: ['advisor' => $this->advisor],
    ), ['email' => $email])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email' => 'A code was recently sent to this email address. Please wait a moment before requesting another.']);
});

it('invalidates prior codes for the same target', function () {
    $student = Student::factory()->create();
    $email = 'invalidate-target@example.com';

    StudentEmailAddress::factory()->create(['sisid' => $student->sisid, 'address' => $email]);

    postJson(URL::signedRoute(
        name: 'widgets.ai.customer-advisors.api.authentication.request',
        /** @phpstan-ignore-next-line */
        parameters: ['advisor' => $this->advisor],
    ), ['email' => $email])
        ->assertSuccessful();

    $firstId = PortalAuthentication::query()
        ->whereMorphedTo('educatable', $student)
        ->where('portal_type', PortalType::CustomerAdvisorWidget)
        ->sole()
        ->getKey();

    Cache::forget(app(AuthenticationCodeRateLimiter::class)->codeRequestKey($student, 'customer-advisor'));

    postJson(URL::signedRoute(
        name: 'widgets.ai.customer-advisors.api.authentication.request',
        /** @phpstan-ignore-next-line */
        parameters: ['advisor' => $this->advisor],
    ), ['email' => $email])
        ->assertSuccessful();

    $liveAuthentications = PortalAuthentication::query()
        ->whereMorphedTo('educatable', $student)
        ->where('portal_type', PortalType::CustomerAdvisorWidget)
        ->get();

    expect($liveAuthentications)->toHaveCount(1);
    expect($liveAuthentications->first()->getKey())->not->toBe($firstId);
});

it('invalidates prior codes for the same prospect', function () {
    $prospect = Prospect::factory()->create();
    $email = 'invalidate-prospect@example.com';

    ProspectEmailAddress::factory()->create(['prospect_id' => $prospect->id, 'address' => $email]);

    postJson(URL::signedRoute(
        name: 'widgets.ai.customer-advisors.api.authentication.request',
        /** @phpstan-ignore-next-line */
        parameters: ['advisor' => $this->advisor],
    ), ['email' => $email])
        ->assertSuccessful();

    $firstId = PortalAuthentication::query()
        ->whereMorphedTo('educatable', $prospect)
        ->where('portal_type', PortalType::CustomerAdvisorWidget)
        ->sole()
        ->getKey();

    Cache::forget(app(AuthenticationCodeRateLimiter::class)->codeRequestKey($prospect, 'customer-advisor'));

    postJson(URL::signedRoute(
        name: 'widgets.ai.customer-advisors.api.authentication.request',
        /** @phpstan-ignore-next-line */
        parameters: ['advisor' => $this->advisor],
    ), ['email' => $email])
        ->assertSuccessful();

    $liveAuthentications = PortalAuthentication::query()
        ->whereMorphedTo('educatable', $prospect)
        ->where('portal_type', PortalType::CustomerAdvisorWidget)
        ->get();

    expect($liveAuthentications)->toHaveCount(1);
    expect($liveAuthentications->first()->getKey())->not->toBe($firstId);
});
