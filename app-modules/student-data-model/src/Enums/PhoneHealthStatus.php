<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\Enums;

use Filament\Support\Contracts\HasLabel;

enum PhoneHealthStatus: string implements HasLabel
{
    case Healthy = 'healthy';

    case Bounced = 'bounced';

    case OptedOut = 'opted_out';

    case NoSmsCapability = 'no_sms_capability';

    public function getLabel(): string
    {
        return match ($this) {
            self::Healthy => 'Healthy',
            self::Bounced => 'Bounced',
            self::OptedOut => 'Opted Out',
            self::NoSmsCapability => 'No SMS Capability',
        };
    }

    public function getTooltipText(): string
    {
        return match ($this) {
            self::Healthy => 'Healthy. No delivery issues detected.',
            self::Bounced => 'Bounced. SMS delivery failed and a bounce was received from our messaging provider.',
            self::OptedOut => 'Opted out. This contact has chosen not to receive SMS communications.',
            self::NoSmsCapability => 'This phone number cannot receive SMS messages.',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Healthy => 'heroicon-m-check-circle',
            self::Bounced => 'heroicon-m-exclamation-triangle',
            self::OptedOut => 'heroicon-m-no-symbol',
            self::NoSmsCapability => 'heroicon-m-no-symbol',
        };
    }

    public function getColorClasses(): string
    {
        return match ($this) {
            self::Healthy => 'text-success-600 dark:text-success-400',
            self::Bounced => 'text-warning-600 dark:text-warning-400',
            self::OptedOut => 'text-danger-500 dark:text-danger-400',
            self::NoSmsCapability => 'text-gray-500 dark:text-gray-400',
        };
    }
}
