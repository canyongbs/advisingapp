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

use App\Models\User;

it('consumes a valid recovery code', function (): void {
    $user = User::factory()->create();
    $user->enableMultifactorAuthentication();

    $recoveryCode = $user->multifactor_recovery_codes[0];

    expect($user->consumeRecoveryCode($recoveryCode))->toBeTrue()
        ->and($user->multifactor_recovery_codes)->toHaveCount(7)->not->toContain($recoveryCode)
        ->and($user->refresh()->multifactor_recovery_codes)->toHaveCount(7)->not->toContain($recoveryCode);
});

it('does not consume an unknown recovery code', function (): void {
    $user = User::factory()->create();
    $user->enableMultifactorAuthentication();

    expect($user->consumeRecoveryCode('not-a-recovery-code'))->toBeFalse()
        ->and($user->refresh()->multifactor_recovery_codes)->toHaveCount(8);
});

it('does not consume the same recovery code twice when the user was loaded before it was used', function (): void {
    $user = User::factory()->create();
    $user->enableMultifactorAuthentication();

    $staleUser = User::query()->findOrFail($user->getKey());

    $recoveryCode = $user->multifactor_recovery_codes[0];

    expect($user->consumeRecoveryCode($recoveryCode))->toBeTrue()
        ->and($staleUser->consumeRecoveryCode($recoveryCode))->toBeFalse();
});

it('keeps both recovery codes consumed when they are used from separately loaded copies of the user', function (): void {
    $user = User::factory()->create();
    $user->enableMultifactorAuthentication();

    [$firstCode, $secondCode] = $user->multifactor_recovery_codes;

    $firstCopy = User::query()->findOrFail($user->getKey());
    $secondCopy = User::query()->findOrFail($user->getKey());

    expect($firstCopy->consumeRecoveryCode($firstCode))->toBeTrue()
        ->and($secondCopy->consumeRecoveryCode($secondCode))->toBeTrue()
        ->and($user->refresh()->multifactor_recovery_codes)
        ->toHaveCount(6)
        ->not->toContain($firstCode)
        ->not->toContain($secondCode);
});
