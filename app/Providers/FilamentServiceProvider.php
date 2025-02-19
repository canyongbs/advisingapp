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

namespace App\Providers;

use App\Models\Export;
use App\Models\FailedImportRow;
use App\Models\Import;
use App\Models\User;
use App\Settings\DisplaySettings;
use Filament\Actions\Exports\Models\Export as BaseExport;
use Filament\Actions\Imports\Models\FailedImportRow as BaseFailedImportRow;
use Filament\Actions\Imports\Models\Import as BaseImport;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BaseExport::class, Export::class);
        $this->app->bind(BaseImport::class, Import::class);
        $this->app->bind(BaseFailedImportRow::class, FailedImportRow::class);
    }

    public function boot(): void
    {
        // Changes to colors also need to be reflected in tailwind.config.js
        FilamentColor::register([
            'danger' => Color::Red,
            'gray' => Color::Zinc,
            'info' => Color::Blue,
            // Trout
            'primary' => [
                50 => '#f6f7f9',
                100 => '#ededf1',
                200 => '#d7d9e0',
                300 => '#b4b9c5',
                400 => '#8b92a5',
                500 => '#6d758a',
                600 => '#575d72',
                700 => '#4d5264',
                800 => '#3e424e',
                900 => '#363944',
                950 => '#24252d',
            ],
            'success' => Color::Green,
            'warning' => Color::Amber,
            'black' => [
                50 => '#f6f6f6',
                100 => '#e7e7e7',
                200 => '#d1d1d1',
                300 => '#b0b0b0',
                400 => '#888888',
                500 => '#6d6d6d',
                600 => '#5d5d5d',
                700 => '#4f4f4f',
                800 => '#454545',
                900 => '#3d3d3d',
                950 => '#000000',
            ],
            'white' => [
                50 => '#ffffff',
                100 => '#efefef',
                200 => '#dcdcdc',
                300 => '#bdbdbd',
                400 => '#989898',
                500 => '#7c7c7c',
                600 => '#656565',
                700 => '#525252',
                800 => '#464646',
                900 => '#3d3d3d',
                950 => '#292929',
            ],
            'dodger-blue' => [
                50 => '#eef3ff',
                100 => '#dae4ff',
                200 => '#bcd0ff',
                300 => '#8fb3ff',
                400 => '#5989ff',
                500 => '#3f69fe',
                600 => '#1d3ef3',
                700 => '#152ae0',
                800 => '#1824b5',
                900 => '#19258f',
                950 => '#141957',
            ],
            'java' => [
                50 => '#f1fcfa',
                100 => '#d1f6f1',
                200 => '#a4ebe3',
                300 => '#6edad1',
                400 => '#40c1bb',
                500 => '#2bb8b3',
                600 => '#1c8583',
                700 => '#1b6a6a',
                800 => '#1a5555',
                900 => '#1a4747',
                950 => '#09292a',
            ],
            'bright-sun' => [
                50 => '#fffbeb',
                100 => '#fff4c6',
                200 => '#fee989',
                300 => '#fed43f',
                400 => '#fec321',
                500 => '#f8a208',
                600 => '#db7a04',
                700 => '#b65607',
                800 => '#94420c',
                900 => '#79370e',
                950 => '#461b02',
            ],
            'jungle-mist' => [
                50 => '#f3f8f8',
                100 => '#e0eded',
                200 => '#bed7d8',
                300 => '#9cc2c4',
                400 => '#6da0a3',
                500 => '#518589',
                600 => '#466e74',
                700 => '#3e5b60',
                800 => '#384e52',
                900 => '#324247',
                950 => '#1e2a2e',
            ],
            'deep-blush' => [
                50 => '#fcf3f9',
                100 => '#fbe8f6',
                200 => '#f8d2ee',
                300 => '#f4addf',
                400 => '#ec7ac8',
                500 => '#e151af',
                600 => '#cf3391',
                700 => '#b32376',
                800 => '#942061',
                900 => '#7c1f53',
                950 => '#4b0c2f',
            ],
        ]);

        FilamentView::registerRenderHook(
            'panels::footer',
            fn (): View => view('filament.footer'),
        );

        DateTimePicker::configureUsing(function (DateTimePicker $component) {
            if ($component instanceof DatePicker) {
                return;
            }

            $timezone = app(DisplaySettings::class)->getTimezone();
            $timezoneLabel = app(DisplaySettings::class)->getTimezoneLabel();

            $component
                ->timezone($timezone)
                ->hintIcon('heroicon-m-clock')
                ->hintIconTooltip("This time is set in {$timezoneLabel}.");
        });

        TextColumn::configureUsing(function (TextColumn $column) {
            $timezone = app(DisplaySettings::class)->getTimezone();
            $timezoneLabel = app(DisplaySettings::class)->getTimezoneLabel();

            $column
                ->timezone(function (TextColumn $column) use ($timezone): ?string {
                    if (! ($column->isTime() || $column->isDateTime())) {
                        return null;
                    }

                    return $timezone;
                })
                ->tooltip(function (TextColumn $column) use ($timezoneLabel): ?string {
                    if (! ($column->isTime() || $column->isDateTime())) {
                        return null;
                    }

                    return "This time is set in {$timezoneLabel}.";
                });
        });

        TextEntry::configureUsing(function (TextEntry $entry) {
            $timezone = app(DisplaySettings::class)->getTimezone();
            $timezoneLabel = app(DisplaySettings::class)->getTimezoneLabel();

            $entry
                ->timezone(function (TextEntry $column) use ($timezone): ?string {
                    if (! ($column->isTime() || $column->isDateTime())) {
                        return null;
                    }

                    return $timezone;
                })
                ->hintIcon(function (TextEntry $entry): ?string {
                    if (! ($entry->isTime() || $entry->isDateTime())) {
                        return null;
                    }

                    return 'heroicon-m-clock';
                })
                ->hintIconTooltip(function (TextEntry $entry) use ($timezoneLabel): ?string {
                    if (! ($entry->isTime() || $entry->isDateTime())) {
                        return null;
                    }

                    return "This time is set in {$timezoneLabel}.";
                });
        });

        Toggle::macro('lockedWithoutAnyLicenses', function (User $user, array $licenses) {
            /** @var Toggle $this */
            return $this->disabled(! $user->hasAnyLicense($licenses))
                ->hintIcon(fn (Toggle $component) => $component->isDisabled() ? 'heroicon-m-lock-closed' : null)
                ->hintIconTooltip('A CRM license is required for our public profile features.');
        });

        Checkbox::macro('lockedWithoutAnyLicenses', function (User $user, array $licenses) {
            /** @var Checkbox $this */
            return $this->disabled(! $user->hasAnyLicense($licenses))
                ->hintIcon(fn (Checkbox $component) => $component->isDisabled() ? 'heroicon-m-lock-closed' : null)
                ->hintIconTooltip('A CRM license is required for our public profile features.');
        });
    }
}
