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

use App\Settings\DisplaySettings;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DailyHoursRepeater
{
    public static function make(string $name): Repeater
    {
        $timezone = app(DisplaySettings::class)->getTimezoneLabel();
        $timeOptions = static::generateTimeOptions();

        return Repeater::make($name)
            ->table([
                TableColumn::make('Day')
                    ->alignStart(),
                TableColumn::make('Enabled')
                    ->alignStart(),
                TableColumn::make("Start Time ({$timezone})")
                    ->alignStart(),
                TableColumn::make("End Time ({$timezone})")
                    ->alignStart(),
            ])
            ->schema([
                TextEntry::make('day')
                    ->formatStateUsing(fn (string $state): string => Str::ucfirst($state)),
                Toggle::make('is_enabled')
                    ->live(),
                Select::make('starts_at')
                    ->validationAttribute('start time')
                    ->placeholder('Select a time')
                    ->options($timeOptions)
                    ->optionsLimit(PHP_INT_MAX)
                    ->required(fn (Get $get) => filled($get('ends_at')))
                    ->hidden(fn (Get $get) => ! $get('is_enabled')),
                Select::make('ends_at')
                    ->validationAttribute('end time')
                    ->placeholder('Select a time')
                    ->options($timeOptions)
                    ->optionsLimit(PHP_INT_MAX)
                    ->required(fn (Get $get) => filled($get('starts_at')))
                    ->hidden(fn (Get $get) => ! $get('is_enabled')),
            ])
            ->extraItemActions([
                Action::make('copyTimesToOtherDays')
                    ->tooltip(fn (Action $action): string => $action->getLabel())
                    ->icon(Heroicon::Clipboard)
                    ->color('gray')
                    ->action(fn (Repeater $component, array $arguments) => $component->rawState(
                        array_map(
                            fn (array $itemState): array => [
                                ...$itemState,
                                ...Arr::only($component->getRawItemState($arguments['item']), [
                                    'starts_at',
                                    'ends_at',
                                ]),
                            ],
                            $component->getRawState(),
                        ),
                    ))
                    ->visible(fn (Repeater $component, array $arguments): bool => $component->getRawItemState($arguments['item'])['is_enabled']),
            ])
            ->reorderable(false)
            ->deletable(false)
            ->addable(false)
            ->minItems(7)
            ->maxItems(7)
            ->default([
                ['day' => 'monday', 'is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                ['day' => 'tuesday', 'is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                ['day' => 'wednesday', 'is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                ['day' => 'thursday', 'is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                ['day' => 'friday', 'is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                ['day' => 'saturday', 'is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
                ['day' => 'sunday', 'is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ]);
    }

    /**
     * @param array{
     *     monday: array{is_enabled: bool, starts_at: ?string, ends_at: ?string},
     *     tuesday: array{is_enabled: bool, starts_at: ?string, ends_at: ?string},
     *     wednesday: array{is_enabled: bool, starts_at: ?string, ends_at: ?string},
     *     thursday: array{is_enabled: bool, starts_at: ?string, ends_at: ?string},
     *     friday: array{is_enabled: bool, starts_at: ?string, ends_at: ?string},
     *     saturday: array{is_enabled: bool, starts_at: ?string, ends_at: ?string},
     *     sunday: array{is_enabled: bool, starts_at: ?string, ends_at: ?string},
     * } $data
     *
     * @return array<array{day: string, is_enabled: bool, starts_at: ?string, ends_at: ?string}>
     */
    public static function mutateDataBeforeFill(array $data): array
    {
        $orderedDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $displayTimezone = app(DisplaySettings::class)->getTimezone();
        $appTimezone = config('app.timezone');

        return array_map(
            function (string $day) use ($data, $displayTimezone, $appTimezone): array {
                $dayData = $data[$day];

                if (filled($dayData['starts_at'] ?? null)) {
                    $dayData['starts_at'] = Carbon::parse($dayData['starts_at'], $appTimezone)
                        ->setTimezone($displayTimezone)
                        ->format('H:i');
                }

                if (filled($dayData['ends_at'] ?? null)) {
                    $dayData['ends_at'] = Carbon::parse($dayData['ends_at'], $appTimezone)
                        ->setTimezone($displayTimezone)
                        ->format('H:i');
                }

                return [
                    'day' => $day,
                    ...$dayData,
                ];
            },
            $orderedDays,
        );
    }

    /**
     * @param array<array{is_enabled: bool, starts_at: ?string, ends_at: ?string}> $data
     *
     * @return array{
     *     monday: array{is_enabled: bool, starts_at: ?string, ends_at: ?string},
     *     tuesday: array{is_enabled: bool, starts_at: ?string, ends_at: ?string},
     *     wednesday: array{is_enabled: bool, starts_at: ?string, ends_at: ?string},
     *     thursday: array{is_enabled: bool, starts_at: ?string, ends_at: ?string},
     *     friday: array{is_enabled: bool, starts_at: ?string, ends_at: ?string},
     *     saturday: array{is_enabled: bool, starts_at: ?string, ends_at: ?string},
     *     sunday: array{is_enabled: bool, starts_at: ?string, ends_at: ?string},
     * }
     */
    public static function mutateDataBeforeSave(array $data): array
    {
        assert(count($data) === 7);

        $displayTimezone = app(DisplaySettings::class)->getTimezone();
        $appTimezone = config('app.timezone');

        return Arr::mapWithKeys(
            $data,
            function (array $itemData, int $index) use ($displayTimezone, $appTimezone): array {
                if (filled($itemData['starts_at'] ?? null)) {
                    $itemData['starts_at'] = Carbon::parse($itemData['starts_at'], $displayTimezone)
                        ->setTimezone($appTimezone)
                        ->format('H:i');
                }

                if (filled($itemData['ends_at'] ?? null)) {
                    $itemData['ends_at'] = Carbon::parse($itemData['ends_at'], $displayTimezone)
                        ->setTimezone($appTimezone)
                        ->format('H:i');
                }

                return [
                    ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'][$index] => $itemData,
                ];
            },
        );
    }

    /**
     * @return array<string, string>
     */
    protected static function generateTimeOptions(): array
    {
        $options = [];
        $displayTimezone = app(DisplaySettings::class)->getTimezone();

        for ($hour = 0; $hour < 24; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 15) {
                $time = Carbon::createFromTime($hour, $minute, 0, config('app.timezone'))
                    ->setTimezone($displayTimezone);

                $value = sprintf('%02d:%02d', $hour, $minute);
                $label = $time->format('g:i A');

                $options[$value] = $label;
            }
        }

        return $options;
    }
}
