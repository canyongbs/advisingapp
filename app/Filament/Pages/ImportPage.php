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

namespace App\Filament\Pages;

use App\Filament\Clusters\ImportExport;
use App\Models\Import;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ImportPage extends Page implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $navigationLabel = 'Import';

    protected static ?string $title = 'Import';

    protected static ?int $navigationSort = 10;

    protected static ?string $cluster = ImportExport::class;

    /**
     * @var array<int|string, bool>
     */
    protected array $importFileExistsCache = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();
        assert($user instanceof User);

        return $user->can('export_hub.view-any');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            EmbeddedTable::make(),
        ]);
    }

    public function table(Table $table): Table
    {
        $canDownload = auth()->user()->can('export_hub.import');

        return $table
            ->query(Import::query()->with('user'))
            ->defaultSort('created_at', 'desc')
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
                    ->visible(fn (Import $record) => $canDownload
                        && $record->completed_at !== null
                        && $this->importFileExists($record)),
            ]);
    }

    protected function importFileExists(Import $import): bool
    {
        return $this->importFileExistsCache[$import->getKey()] ??= Storage::disk('s3')->exists("imports/{$import->getKey()}.csv");
    }
}
