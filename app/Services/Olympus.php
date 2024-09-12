<?php

namespace App\Services;

use App\Settings\OlympusSettings;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Spatie\Multitenancy\Landlord;

class Olympus
{
    public static function makeRequest(): PendingRequest
    {
        [$olympusUrl, $olympusKey] = Landlord::execute(function (): array {
            $settings = app(OlympusSettings::class);

            return [
                rtrim($settings->url, '/'),
                $settings->key,
            ];
        });

        return Http::withToken($olympusKey)
                ->baseUrl($olympusUrl);
    }
}