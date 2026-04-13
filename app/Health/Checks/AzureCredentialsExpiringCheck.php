<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
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

namespace App\Health\Checks;

use AdvisingApp\Authorization\Exceptions\NoMatchingAzureCredentialsException;
use AdvisingApp\Authorization\Settings\AzureSsoSettings;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use stdClass;

class AzureCredentialsExpiringCheck extends Check
{
    public function run(): Result
    {
        try {
            $azureSsoSettings = app(AzureSsoSettings::class);

            $response = Http::asForm()
                ->post(
                    'https://login.microsoftonline.com/' . $azureSsoSettings->tenant_id . '/oauth2/v2.0/token',
                    [
                        'client_id' => $azureSsoSettings->client_id,
                        'client_secret' => $azureSsoSettings->client_secret,
                        'grant_type' => 'client_credentials',
                        'scope' => 'https://graph.microsoft.com/.default',
                    ]
                )
                ->throw();

            $data = Http::withToken($response->object()->access_token)
                ->get("https://graph.microsoft.com/v1.0/applications(appId='{$azureSsoSettings->client_id}')" . '?$select=passwordCredentials')
                ->throw();

            /** @var Collection<int, stdClass> $passwordCredentials */
            $passwordCredentials = $data->object()->passwordCredentials;

            $credentials = collect($passwordCredentials)->filter(function (stdClass $item) use ($azureSsoSettings) {
                return Str::startsWith($azureSsoSettings->client_secret, $item->hint);
            });

            if ($credentials->isEmpty()) {
                throw new NoMatchingAzureCredentialsException();
            }

            $endDateTime = Carbon::parse($credentials->sortBy(fn (stdClass $item) => Carbon::parse($item->endDateTime))->first()->endDateTime);

            if ($endDateTime->isPast()) {
                return Result::make()->failed();
            }

            if ($endDateTime->lte(now()->addDays(45))) {
                return Result::make()->warning();
            }
        } catch (Exception $exception) {
            report($exception);
        }

        return Result::make()->ok();
    }
}
