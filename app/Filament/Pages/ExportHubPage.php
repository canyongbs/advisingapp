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

namespace App\Filament\Pages;

use App\Features\ExportHubFeature;
use App\Models\Export;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use UnitEnum;

class ExportHubPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected string $view = 'filament.pages.export-hub-page';

    protected static string | UnitEnum | null $navigationGroup = 'Data and Analytics';

    protected static ?string $navigationLabel = 'Export Hub';

    protected static ?string $title = 'Export Hub';

    protected static ?int $navigationSort = 30;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        assert($user instanceof User);

        return ExportHubFeature::active() && $user->can('export_hub.view-any');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Export::query())
            ->columns([
                TextColumn::make('requestor')
                    ->getStateUsing(function (Export $record): ?string {
                        return $record->user->name ?? null;
                    }),
                TextColumn::make('exporter')
                    ->label('Export Name')
                    ->getStateUsing(function (Export $record): string {
                        if (defined($record->exporter . '::EXPORT_NAME')) {
                            return constant($record->exporter . '::EXPORT_NAME') . ' Export';
                        }

                        return Str::of(class_basename($record->exporter))
                            ->replaceLast('Exporter', '')
                            ->headline() . ' Export';
                    }),
                TextColumn::make('created_at')
                    ->label('Date Started')
                    ->dateTime(),
                TextColumn::make('completed_at')
                    ->label('Date Completed')
                    ->dateTime(),
            ])->recordActions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Export $record) => URL::signedRoute('exports.download', $record))
                    ->visible(fn (Export $record) => $record->completed_at !== null && auth()->user()->can('export_hub.import')),
            ]);
    }
}
