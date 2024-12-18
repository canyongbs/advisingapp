<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Notification\Enums;

use App\Enums\Integration;
use App\Exceptions\IntegrationException;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum NotificationChannel: string implements HasLabel, HasIcon
{
    case Email = 'email';
    case Sms = 'sms';
    case Database = 'database';

    public function getLabel(): string
    {
        return match ($this) {
            static::Sms => 'Text',
            default => $this->name,
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            NotificationChannel::Email => 'heroicon-o-envelope',
            NotificationChannel::Sms => 'heroicon-o-chat-bubble-bottom-center-text',
            NotificationChannel::Database => 'heroicon-o-bell',
        };
    }

    public function getDefault(): NotificationChannel
    {
        return NotificationChannel::Email;
    }

    public function getCaseDisabled(): bool
    {
        return $this->caseDependsOnIntegration()?->isOff() ?? false;
    }

    public function getLabelForIntegrationState(): string
    {
        $integration = $this->caseDependsOnIntegration();

        return $this->getCaseDisabled() && $integration
            ? sprintf(
                '%s (%s)',
                $this->getLabel(),
                IntegrationException::make($integration)->getMessage()
            )
            : $this->getLabel();
    }

    public static function getEngagementOptions(): array
    {
        return [
            NotificationChannel::Email->value => NotificationChannel::Email->getLabelForIntegrationState(),
            NotificationChannel::Sms->value => NotificationChannel::Sms->getLabelForIntegrationState(),
        ];
    }

    public static function parse(string | self | null $value): ?self
    {
        if (blank($value)) {
            return null;
        }

        if ($value instanceof self) {
            return $value;
        }

        return self::tryFrom($value);
    }

    private function caseDependsOnIntegration(): ?Integration
    {
        return match ($this) {
            NotificationChannel::Sms => Integration::Twilio,
            default => null,
        };
    }
}
