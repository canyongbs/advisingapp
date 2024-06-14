<?php

namespace AdvisingApp\MultifactorAuthentication\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use AdvisingApp\MultifactorAuthentication\Services\MultifactorService;

trait MultifactorAuthenticatable
{
    public function hasEnabledTwoFactor(): bool
    {
        return ! is_null($this->multifactor_secret);
    }

    public function hasConfirmedTwoFactor(): bool
    {
        return ! is_null($this->multifactor_secret) && ! is_null($this->multifactor_confirmed_at);
    }

    public function twoFactorRecoveryCodes(): Attribute
    {
        return Attribute::make(
            get: fn () => json_decode(decrypt(
                $this->multifactor_recovery_codes
            ), true)
        );
    }

    public function twoFactorSecret(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->multifactor_secret
        );
    }

    public function enableTwoFactorAuthentication()
    {
        $this->disableTwoFactorAuthentication();

        $this->forceFill([
            'multifactor_secret' => encrypt(app(MultifactorService::class)->getEngine()->generateSecretKey()),
            'multifactor_recovery_codes' => $this->generateRecoveryCodes(),
        ])->save();
    }

    public function disableTwoFactorAuthentication()
    {
        $this->forceFill(
            [
                'multifactor_secret' => null,
                'multifactor_recovery_codes' => null,
                'multifactor_confirmed_at' => null,
            ]
        )->save();
    }

    public function confirmTwoFactorAuthentication()
    {
        // event(new LoginSuccess($this->authenticatable));

        $this->forceFill([
            'multifactor_confirmed_at' => now(),
        ])->save();
    }

    public function generateRecoveryCodes()
    {
        return encrypt(json_encode(Collection::times(8, function () {
            return Str::random(10) . '-' . Str::random(10);
        })->all()));
    }

    public function destroyRecoveryCode(string $recoveryCode): void
    {
        $unusedCodes = array_filter($this->multifactor_recovery_codes ?? [], fn ($code) => $code !== $recoveryCode);

        // TODO: Look into what this does
        $this->forceFill([
            'multifactor_recovery_codes' => $unusedCodes ? encrypt(json_encode($unusedCodes)) : null,
        ])->save();
    }

    public function getTwoFactorQrCodeUrl()
    {
        return app(MultifactorService::class)->getQrCodeUrl(
            config('app.name'),
            $this->email,
            decrypt($this->multifactor_secret)
        );
    }

    public function reGenerateRecoveryCodes()
    {
        $this->forceFill([
            'multifactor_recovery_codes' => $this->generateRecoveryCodes(),
        ])->save();
    }
}
