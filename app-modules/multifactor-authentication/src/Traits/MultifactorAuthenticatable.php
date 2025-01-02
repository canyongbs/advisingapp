<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\MultifactorAuthentication\Traits;

use AdvisingApp\MultifactorAuthentication\Services\MultifactorService;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
            get: fn (?string $value) => $value ? json_decode(decrypt(
                $value
            ), true) : null
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
            config('app.name') . ' | ' . Tenant::current()->name,
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
