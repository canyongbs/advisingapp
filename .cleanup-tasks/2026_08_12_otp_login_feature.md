---
title: Otp Login Feature
created: 2026-08-12
---

## Feature Flags

- App\Features\OtpLoginFeature

## Temporary Migrations

## Additional Cleanup

Once `OtpLoginFeature` is active for every tenant, the legacy "magic link only" flow becomes
entirely dead. In each file below, delete the exact inactive-branch code shown, then unwrap the
remaining `if (OtpLoginFeature::active()) { ... }` so its body runs unconditionally, and remove
the now-unused `App\Features\OtpLoginFeature` import.

**`app-modules/authorization/src/Http/Controllers/GenerateOtpLoginCodeController.php`** — delete below section:

```php
            ['otpCode' => $otpCode, 'code' => $code] = DB::transaction(function () use ($user): array {
                .....................
            return response()->json([
                'link' => URL::temporarySignedRoute(
                    name: 'otp-code.login',
                    expiration: now()->addMinutes(20)->toImmutable(),
                    parameters: [
                        'otpCode' => $otpCode->getKey(),
                    ]
                ),
            ]);
```

Then also remove the now-unused `OtpLoginCode`, `Hash`, and `DB` imports.

**`app-modules/authorization/src/Http/Controllers/Auth/OneTimeLoginController.php`** — delete:

```php
        auth()->login($user);

        return redirect(Filament::getUrl());
```

Then also remove the now-unused `Filament\Facades\Filament` import.

**`app/Notifications/SetPasswordNotification.php`** — delete:

```php
        } else {
            $message->line('For security reasons, this link will expire in 24 hours.');
        }
```

(keeping the `if (OtpLoginFeature::active()) { ... }` body's closing `}` in its place), so the
`$code = app(GenerateOtpLoginCode::class)($notifiable, $expiresAt);` block always runs.

**`app-modules/authorization/src/Models/OtpLoginCode.php`** (`prunable()`) — delete:

```php
        return static::query()->where('created_at', '<=', now()->subMinutes(20));
```

Delete these files entirely — they only exist to serve the inactive ("magic link only") flow and
have no active-flow branch to fall back to:

- `app-modules/authorization/src/Http/Controllers/OtpLoginCodeController.php`
- `app-modules/authorization/src/Http/Controllers/VerifyOtpLoginCodeController.php`
- `app-modules/authorization/resources/views/otp-entry.blade.php`
- `app-modules/authorization/tests/Tenant/Http/Controllers/OtpLoginCodeControllerTest.php`
- `app-modules/authorization/tests/Tenant/Http/Controllers/VerifyOtpLoginCodeControllerTest.php`
- The `otp-code.login` and `otp-code.verify` route registrations in
  `app-modules/authorization/routes/web.php` (and their `OtpLoginCodeController` /
  `VerifyOtpLoginCodeController` imports)

