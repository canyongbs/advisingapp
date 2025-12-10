<?php

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
            ->columns($hasDays ? 3 : 2)
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
