<?php

namespace AdvisingApp\Form\Actions;

use AdvisingApp\Form\Models\Secret;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class StoreSecret
{
    public function __invoke(#[SensitiveParameter] ?string $value, ?string $previousSecretId = null): ?Secret
    {
        return DB::transaction(function () use ($previousSecretId, $value): ?Secret {
            if (filled($previousSecretId)) {
                $previousSecret = Secret::query()
                    ->whereKey($previousSecretId)
                    ->whereNull('related_id')
                    ->lockForUpdate()
                    ->first();

                if (! $previousSecret) {
                    throw ValidationException::withMessages([
                        'previous_secret_id' => 'The previous password is no longer available.',
                    ]);
                }

                if (blank($value)) {
                    $previousSecret->delete();

                    return null;
                }

                $previousSecret->value = Crypt::encryptString($value);
                $previousSecret->save();

                return $previousSecret;
            }

            if (blank($value)) {
                return null;
            }

            $secret = new Secret();
            $secret->value = Crypt::encryptString($value);
            $secret->save();

            return $secret;
        });
    }
}
