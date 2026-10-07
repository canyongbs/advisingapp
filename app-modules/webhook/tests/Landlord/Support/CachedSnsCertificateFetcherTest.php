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

use AdvisingApp\Webhook\Support\CachedSnsCertificateFetcher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Exceptions;

it('returns a cached certificate without fetching it', function () {
    $cache = Cache::store('array');
    $cache->put('sns-signing-certificate:https://sns.us-west-2.amazonaws.com/certificate.pem', 'CERTIFICATE');

    expect((new CachedSnsCertificateFetcher($cache))('https://sns.us-west-2.amazonaws.com/certificate.pem'))->toBe('CERTIFICATE');
});

it('fetches and caches a certificate it has not seen', function () {
    $certificatePath = tempnam(sys_get_temp_dir(), 'sns');
    file_put_contents($certificatePath, 'CERTIFICATE');

    $cache = Cache::store('array');

    expect((new CachedSnsCertificateFetcher($cache))($certificatePath))->toBe('CERTIFICATE')
        ->and($cache->get("sns-signing-certificate:{$certificatePath}"))->toBe('CERTIFICATE');

    unlink($certificatePath);
});

it('reports a failed fetch without caching it', function () {
    Exceptions::fake();

    $certificatePath = sys_get_temp_dir() . '/missing-sns-certificate.pem';

    $cache = Cache::store('array');

    expect((new CachedSnsCertificateFetcher($cache))($certificatePath))->toBeFalse()
        ->and($cache->has("sns-signing-certificate:{$certificatePath}"))->toBeFalse();

    Exceptions::assertReported(ErrorException::class);
});
