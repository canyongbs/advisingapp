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

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\FusedGroup;

class DurationInput
{
    public static function make(string $name, bool $isRequired, bool $hasDays): FusedGroup
    {
        return FusedGroup::make([
            TextInput::make('days')
                ->suffix('days')
                ->required($isRequired)
                ->default(0)
                ->minValue(0)
                ->maxValue(27)
                ->integer()
                ->visible($hasDays),
            TextInput::make('hours')
                ->suffix('hours')
                ->required($isRequired)
                ->default(0)
                ->minValue(0)
                ->maxValue(23)
                ->integer(),
            TextInput::make('minutes')
                ->suffix('minutes')
                ->required($isRequired)
                ->default(0)
                ->minValue(0)
                ->maxValue(59)
                ->integer(),
        ])
            ->gridContainer()
            ->columns($hasDays ? ['default' => 1, '@sm' => 3, '!@sm' => 3] : ['default' => 1, '@3xs' => 2, '!@sm' => 2])
            ->statePath($name);
    }

    /**
     * @return array{days?: int, hours: int, minutes: int}
     */
    public static function mutateDataBeforeFill(int $totalMinutes, bool $hasDays): array
    {
        $days = 0;

        if ($hasDays) {
            $days = intdiv($totalMinutes, 1440);
            $totalMinutes -= $days * 1440;
        }

        $hours = intdiv($totalMinutes, 60);

        $totalMinutes -= $hours * 60;

        return [
            ...($hasDays ? ['days' => $days] : []),
            'hours' => $hours,
            'minutes' => $totalMinutes,
        ];
    }

    /**
     * @param array{days?: int, hours: int, minutes: int} $data
     *
     * @return int
     */
    public static function mutateDataBeforeSave(array $data): int
    {
        $totalMinutes = 0;

        if ($data['days'] ?? null) {
            $totalMinutes += $data['days'] * 24 * 60;
        }

        if ($data['hours']) {
            $totalMinutes += $data['hours'] * 60;
        }

        if ($data['minutes']) {
            $totalMinutes += $data['minutes'];
        }

        return $totalMinutes;
    }
}
