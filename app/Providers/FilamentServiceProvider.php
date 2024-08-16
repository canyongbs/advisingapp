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

namespace App\Providers;

use App\Models\User;
use App\Models\Export;
use App\Models\Import;
use Illuminate\View\View;
use Laravel\Pennant\Feature;
use App\Models\FailedImportRow;
use App\Settings\DisplaySettings;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\ServiceProvider;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Facades\FilamentView;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\DateTimePicker;
use Filament\Actions\Exports\Models\Export as BaseExport;
use Filament\Actions\Imports\Models\Import as BaseImport;
use Filament\Actions\Imports\Models\FailedImportRow as BaseFailedImportRow;

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
        FilamentView::registerRenderHook(
            'panels::footer',
            fn (): View => view('filament.footer'),
        );

        DateTimePicker::configureUsing(function (DateTimePicker $component) {
            if ($component instanceof DatePicker) {
                return;
            }

            $timezone = Feature::active('display-settings') ?
                app(DisplaySettings::class)->getTimezone() :
                auth()->user()->timezone;
            $timezoneLabel = Feature::active('display-settings') ?
                app(DisplaySettings::class)->getTimezoneLabel() :
                auth()->user()->timezone;

            $component
                ->timezone($timezone)
                ->hintIcon('heroicon-m-clock')
                ->hintIconTooltip("This time is set in {$timezoneLabel}.");
        });

        TextColumn::configureUsing(function (TextColumn $column) {
            $timezone = Feature::active('display-settings') ?
                app(DisplaySettings::class)->getTimezone() :
                auth()->user()->timezone;
            $timezoneLabel = Feature::active('display-settings') ?
                app(DisplaySettings::class)->getTimezoneLabel() :
                auth()->user()->timezone;

            $column
                ->timezone($timezone)
                ->tooltip(function (TextColumn $column) use ($timezoneLabel): ?string {
                    if (! ($column->isTime() || $column->isDateTime())) {
                        return null;
                    }

                    return "This time is set in {$timezoneLabel}.";
                });
        });

        TextEntry::configureUsing(function (TextEntry $entry) {
            $timezone = Feature::active('display-settings') ?
                app(DisplaySettings::class)->getTimezone() :
                auth()->user()->timezone;
            $timezoneLabel = Feature::active('display-settings') ?
                app(DisplaySettings::class)->getTimezoneLabel() :
                auth()->user()->timezone;

            $entry
                ->timezone($timezone)
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
