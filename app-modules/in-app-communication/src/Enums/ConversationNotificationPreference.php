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

namespace AdvisingApp\InAppCommunication\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ConversationNotificationPreference: string implements HasColor, HasDescription, HasLabel, HasIcon
{
    case All = 'all';

    case Mentions = 'mentions';

    case None = 'none';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::All => 'All messages',
            self::Mentions => 'Mentions only',
            self::None => 'Mute',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::None => 'gray',
            default => 'warning',
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::All => 'Receive notifications for all messages.',
            self::Mentions => 'Receive notifications for messages that mention you.',
            self::None => 'Do not receive notifications for any messages.',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::All => 'heroicon-m-bell-alert',
            self::Mentions => 'heroicon-m-at-symbol',
            self::None => 'heroicon-m-bell-slash',
        };
    }
}
