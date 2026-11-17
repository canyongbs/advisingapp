<?php

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
