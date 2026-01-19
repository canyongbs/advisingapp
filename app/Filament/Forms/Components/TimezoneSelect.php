<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace App\Filament\Forms\Components;

use DateTime;
use DateTimeZone;
use Filament\Forms\Components\Select;

class TimezoneSelect
{
    protected const EXCLUDED_PREFIXES = [
        'US/',
        'Canada/',
        'Mexico/',
        'Brazil/',
        'Chile/',
        'Australia/',
        'NZ/',
        'Etc/GMT',
        'Etc/UTC',
    ];

    protected const EXCLUDED_IDENTIFIERS = [
        'America/Edmonton',
        'America/Whitehorse',
        'America/Inuvik',
        'America/Fort_Nelson',
        'America/Coral_Harbour',
        'America/Atikokan',
        'America/Shiprock',
        'America/Indiana/Indianapolis',
        'America/Knox_IN',
    ];

    protected const US_TIMEZONES = [
        'Pacific/Honolulu',
        'America/Anchorage',
        'America/Los_Angeles',
        'America/Denver',
        'America/Phoenix',
        'America/Chicago',
        'America/New_York',
        'America/Puerto_Rico',
    ];

    public static function make(string $name = 'timezone'): Select
    {
        $now = new DateTime('now', new DateTimeZone('UTC'));

        $identifiers = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

        $usTimezones = [];
        $otherTimezones = [];

        foreach ($identifiers as $id) {
            if (! self::isCanonical($id)) {
                continue;
            }

            $timezone = new DateTimeZone($id);
            $offset = $timezone->getOffset($now);

            $entry = [
                'id' => $id,
                'offset' => $offset,
                'label' => self::formatLabel($id, $offset),
            ];

            if (in_array($id, self::US_TIMEZONES, true)) {
                $usTimezones[] = $entry;
            } else {
                $otherTimezones[] = $entry;
            }
        }

        $sortByOffset = static function (array $first, array $second): int {
            return $first['offset'] <=> $second['offset']
                ?: strcmp($first['id'], $second['id']);
        };

        usort($usTimezones, $sortByOffset);
        usort($otherTimezones, $sortByOffset);

        return Select::make($name)
            ->searchable()
            ->optionsLimit(PHP_INT_MAX)
            ->options([
                'United States' => self::toOptions($usTimezones),
                'Other Timezones' => self::toOptions($otherTimezones),
            ]);
    }

    protected static function isCanonical(string $id): bool
    {
        if ((! str_contains($id, '/')) && ($id !== 'UTC')) {
            return false;
        }

        foreach (self::EXCLUDED_PREFIXES as $prefix) {
            if (str_starts_with($id, $prefix)) {
                return false;
            }
        }

        return ! in_array($id, self::EXCLUDED_IDENTIFIERS, true);
    }

    /**
     * @param array<int, array{id: string, label: string}> $zones
     *
     * @return array<string, string>
     */
    protected static function toOptions(array $zones): array
    {
        $options = [];

        foreach ($zones as $zone) {
            $options[$zone['id']] = $zone['label'];
        }

        return $options;
    }

    protected static function formatLabel(string $id, int $offset): string
    {
        if ($id === 'UTC') {
            return 'UTC';
        }

        $sign = $offset >= 0 ? '+' : '-';
        $hours = intdiv(abs($offset), 3600);
        $minutes = intdiv(abs($offset) % 3600, 60);

        $utc = sprintf('UTC%s%02d:%02d', $sign, $hours, $minutes);

        [, $city] = array_pad(explode('/', $id, 2), 2, null);

        $city = $city
            ? str_replace('_', ' ', $city)
            : $id;

        return sprintf('(%s) %s (%s)', $utc, $id, $city);
    }
}
