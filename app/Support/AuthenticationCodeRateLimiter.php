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

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthenticationCodeRateLimiter
{
    public const MAX_ATTEMPTS = 5;

    public const DECAY_SECONDS = 86400;

    public const MAX_CODE_REQUESTS = 1;

    public const CODE_REQUEST_DECAY_SECONDS = 60;

    public function isLocked(Model $authentication): bool
    {
        return RateLimiter::tooManyAttempts($this->key($authentication), self::MAX_ATTEMPTS);
    }

    public function recordFailedAttempt(Model $authentication): void
    {
        RateLimiter::hit($this->key($authentication), self::DECAY_SECONDS);
    }

    public function clear(Model $authentication): void
    {
        RateLimiter::clear($this->key($authentication));
    }

    public function key(Model $authentication): string
    {
        return 'authentication-code:' . $authentication::class . ':' . $authentication->getKey();
    }

    /**
     * Enforce a per-target cooldown so a fresh code cannot be minted repeatedly to
     * bypass the per-record guess lockout by rotating authentication records.
     */
    public function ensureCanRequestCode(Model $author, string $scope): void
    {
        if (RateLimiter::tooManyAttempts($this->codeRequestKey($author, $scope), self::MAX_CODE_REQUESTS)) {
            throw ValidationException::withMessages([
                'email' => 'A code was recently sent to this email address. Please wait a moment before requesting another.',
            ]);
        }
    }

    public function recordCodeRequest(Model $author, string $scope): void
    {
        RateLimiter::hit($this->codeRequestKey($author, $scope), self::CODE_REQUEST_DECAY_SECONDS);
    }

    public function codeRequestKey(Model $author, string $scope): string
    {
        return 'authentication-code-request:' . $author::class . ':' . $author->getKey() . ':' . $scope;
    }
}
