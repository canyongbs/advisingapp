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

namespace AdvisingApp\Authorization\Enums;

use AdvisingApp\Authorization\Exceptions\InvalidAzureMatchingProperty;
use AdvisingApp\Authorization\Settings\AzureSsoSettings;
use AdvisingApp\Authorization\Settings\GoogleSsoSettings;
use AdvisingApp\MeetingCenter\Settings\AzureCalendarSettings;
use Exception;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Mockery\MockInterface;
use SocialiteProviders\Azure\User;
use SocialiteProviders\Manager\Config;

enum SocialiteProvider: string
{
    case Azure = 'azure';

    case AzureCalendar = 'azure_calendar';

    case Google = 'google';

    public function driver(): Provider|MockInterface
    {
        return Socialite::driver(
            match ($this->value) {
                'azure', 'azure_calendar' => 'azure',
                'google' => 'google',
                default => throw new Exception('Invalid socialite provider'),
            }
        );
    }

    public function config(): Config
    {
        $azureSsoSettings = app(AzureSsoSettings::class);

        $azureCalendarSettings = app(AzureCalendarSettings::class);

        $googleSsoSettings = app(GoogleSsoSettings::class);

        return match ($this->value) {
            'azure' => new Config(
                $azureSsoSettings->client_id,
                $azureSsoSettings->client_secret,
                route('socialite.callback', ['provider' => 'azure']),
                ['tenant' => $azureSsoSettings->tenant_id ?? 'common']
            ),
            'azure_calendar' => new Config(
                key: $azureCalendarSettings->client_id,
                secret: $azureCalendarSettings->client_secret,
                callbackUri: route('calendar.outlook.callback'),
                additionalProviderConfig: ['tenant' => $azureCalendarSettings->tenant_id ?? 'common']
            ),
            'google' => new Config(
                $googleSsoSettings->client_id,
                $googleSsoSettings->client_secret,
                route('socialite.callback', ['provider' => 'google'])
            ),
            default => throw new Exception('Invalid socialite provider'),
        };
    }

    public function getEmailFromUser(mixed $user): string
    {
        return match ($this->value) {
            'azure', 'azure_calendar' => (function () use ($user) {
                /** @var User $user */

                return match (app(AzureSsoSettings::class)->matching_property) {
                    AzureMatchingProperty::UserPrincipalName => $user->getPrincipalName(),
                    AzureMatchingProperty::Mail => $user->getMail(),
                    default => throw new InvalidAzureMatchingProperty(app(AzureSsoSettings::class)->matching_property),
                };
            })(),
            'google' => $user->getEmail(),
            default => throw new Exception('Invalid socialite provider'),
        };
    }
}
