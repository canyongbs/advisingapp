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

use AdvisingApp\StudentDataModel\Jobs\LookupPhoneNumber;
use AdvisingApp\StudentDataModel\Jobs\Middleware\SkipWhilePhoneNumberLookupIsRateLimited;
use Illuminate\Support\Facades\Cache;

use function Pest\Laravel\travelTo;

it('releases the job for the remaining window when the rate limit is currently active', function () {
    travelTo(now()->startOfMinute());

    Cache::put(
        SkipWhilePhoneNumberLookupIsRateLimited::cacheKey(),
        now()->timestamp + 30,
        30,
    );

    $job = Mockery::mock(LookupPhoneNumber::class);
    $job->shouldReceive('release')->once()->with(30); // @phpstan-ignore method.notFound

    $next = fn () => $this->fail('Expected the middleware to release the job, not pass it through.');

    (new SkipWhilePhoneNumberLookupIsRateLimited())->handle($job, $next); // @phpstan-ignore argument.type
});

it('passes the job through when no rate limit window is active', function () {
    Cache::forget(SkipWhilePhoneNumberLookupIsRateLimited::cacheKey());

    $job = Mockery::mock(LookupPhoneNumber::class);
    $job->shouldReceive('release')->never(); // @phpstan-ignore method.notFound

    $called = false;
    $next = function () use (&$called): void {
        $called = true;
    };

    (new SkipWhilePhoneNumberLookupIsRateLimited())->handle($job, $next); // @phpstan-ignore argument.type

    expect($called)->toBeTrue();
});

it('passes the job through when the cached window has already elapsed', function () {
    travelTo(now()->startOfMinute());

    Cache::put(
        SkipWhilePhoneNumberLookupIsRateLimited::cacheKey(),
        now()->timestamp - 1,
        60,
    );

    $job = Mockery::mock(LookupPhoneNumber::class);
    $job->shouldReceive('release')->never(); // @phpstan-ignore method.notFound

    $called = false;
    $next = function () use (&$called): void {
        $called = true;
    };

    (new SkipWhilePhoneNumberLookupIsRateLimited())->handle($job, $next); // @phpstan-ignore argument.type

    expect($called)->toBeTrue();
});
