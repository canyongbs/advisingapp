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

enum PhoneNumberLookupStatus: string implements HasLabel
{
    case ValidMobile = 'valid_mobile';

    case ValidLandline = 'valid_landline';

    case ValidVoip = 'valid_voip';

    case ValidTollFree = 'valid_toll_free';

    case Invalid = 'invalid';

    case Unknown = 'unknown';

    case LookupFailed = 'lookup_failed';

    public function getLabel(): string
    {
        return match ($this) {
            self::ValidMobile => 'Valid (Mobile)',
            self::ValidLandline => 'Valid (Landline)',
            self::ValidVoip => 'Valid (VoIP)',
            self::ValidTollFree => 'Valid (Toll-Free)',
            self::Invalid => 'Invalid',
            self::Unknown => 'Unknown',
            self::LookupFailed => 'Lookup Failed',
        };
    }

    /**
     * Statuses that confirm a number can receive SMS, per carrier lookup.
     * Landlines, invalid numbers, and inconclusive results are intentionally
     * excluded — only an affirmative textable classification counts.
     *
     * @return list<self>
     */
    public static function textableStatuses(): array
    {
        return [
            self::ValidMobile,
            self::ValidVoip,
            self::ValidTollFree,
        ];
    }

    public function isTextable(): bool
    {
        return in_array($this, self::textableStatuses(), strict: true);
    }

    /**
     * Map a Telnyx Number Lookup `carrier.type` value to an internal status.
     *
     * Telnyx returns a successful lookup with a carrier object whose `type`
     * describes the line type. The exact strings vary, so we normalize and
     * match loosely. A successful lookup with no recognizable type is treated
     * as `Unknown` rather than `Invalid`.
     */
    public static function fromTelnyxCarrierType(?string $carrierType): self
    {
        $normalized = strtolower(trim((string) $carrierType)); // @phpstan-ignore Common.noStrtolower

        if ($normalized === '') {
            return self::Unknown;
        }

        return match (true) {
            str_contains($normalized, 'mobile') => self::ValidMobile,
            str_contains($normalized, 'toll') => self::ValidTollFree,
            str_contains($normalized, 'voip') => self::ValidVoip,
            str_contains($normalized, 'landline'), str_contains($normalized, 'fixed') => self::ValidLandline,
            default => self::Unknown,
        };
    }
}
