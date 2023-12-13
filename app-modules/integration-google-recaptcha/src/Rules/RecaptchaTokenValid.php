<?php

namespace Assist\IntegrationGoogleRecaptcha\Rules;

use Closure;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Validation\ValidationRule;
use Assist\IntegrationGoogleRecaptcha\Settings\GoogleRecaptchaSettings;

class RecaptchaTokenValid implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (blank($value)) {
            $fail('The recaptcha token was not provided.');
        }

        $settings = app(GoogleRecaptchaSettings::class);

        try {
            $response = Http::asForm()
                ->retry(3, 100)
                ->post(config('services.google_recaptcha.url'), [
                    'secret' => $settings->secret_key,
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ])
                ->throw();

            // TODO Figure out how we actually want to handle low scores
            if ($response->json('score') < 0.5) {
                $fail('The recaptcha score was too low.');
            }
        } catch (Exception $e) {
            $fail('The recaptcha token was invalid.');
        }
    }
}
