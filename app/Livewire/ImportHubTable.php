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

namespace App\Livewire;

use App\Models\Import;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Livewire\Component;

class ImportHubTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Import::query())
            ->columns([
                TextColumn::make('requestor')
                    ->getStateUsing(function (Import $record): ?string {
                        return $record->user->name ?? null;
                    }),
                TextColumn::make('importer')
                    ->label('Import Name')
                    ->getStateUsing(function (Import $record): string {
                        if (defined($record->importer . '::IMPORT_NAME')) {
                            return constant($record->importer . '::IMPORT_NAME') . ' Import';
                        }

                        return Str::of(class_basename($record->importer))
                            ->replaceLast('Importer', '')
                            ->headline() . ' Import';
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
                    ->tooltip(fn (Import $record) => $record->total_rows ? 'Number of Rows: ' . number_format($record->total_rows) : null)
                    ->url(fn (Import $record) => URL::signedRoute('imports.download', $record))
                    ->visible(fn (Import $record) => $record->completed_at !== null
                        && Storage::disk('s3')->exists("imports/{$record->getKey()}.csv")
                        && auth()->user()->can('export_hub.import')),
            ]);
    }

    public function render(): View
    {
        return view('livewire.import-hub-table');
    }
}
