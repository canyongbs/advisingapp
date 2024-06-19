<?php

namespace AdvisingApp\MultifactorAuthentication\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use AdvisingApp\MultifactorAuthentication\Services\MultifactorService;

trait MultifactorAuthenticatable
{
    public function hasEnabledMultifactor(): bool
    {
        return ! is_null($this->multifactor_secret);
    }

    public function hasConfirmedMultifactor(): bool
    {
        return ! is_null($this->multifactor_secret) && ! is_null($this->multifactor_confirmed_at);
    }

    public function multifactorRecoveryCodes(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => json_decode(decrypt(
                $value
            ), true)
        );
    }

    public function enableMultifactorAuthentication()
    {
        $this->disableMultifactorAuthentication();

        $this->forceFill([
            'multifactor_secret' => encrypt(app(MultifactorService::class)->getEngine()->generateSecretKey()),
            'multifactor_recovery_codes' => $this->generateRecoveryCodes(),
        ])->save();
    }

    public function disableMultifactorAuthentication()
    {
        $this->forceFill(
            [
                'multifactor_secret' => null,
                'multifactor_recovery_codes' => null,
                'multifactor_confirmed_at' => null,
            ]
        )->save();
    }

    public function confirmMultifactorAuthentication()
    {
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

        $this->forceFill([
            'multifactor_recovery_codes' => $unusedCodes ? encrypt(json_encode($unusedCodes)) : null,
        ])->save();
    }

    public function getMultifactorQrCodeUrl()
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
