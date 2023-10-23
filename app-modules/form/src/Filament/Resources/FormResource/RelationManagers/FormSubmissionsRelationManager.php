<?php

namespace Assist\Form\Filament\Resources\FormResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Maatwebsite\Excel\Excel;
use App\Filament\Columns\IdColumn;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use pxlrbt\FilamentExcel\Columns\Column;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use App\Filament\Resources\RelationManagers\RelationManager;

class FormSubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                KeyValue::make('content')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                IdColumn::make(),
                TextColumn::make('created_at')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->headerActions([
                ExportAction::make()
                    ->exports([
                        ExcelExport::make()
                            ->modifyQueryUsing(function ($query) {
                                return $query->where('form_id', $this->getOwnerRecord()->id);
                            })
                            ->withFilename(function () {
                                return str("form-submissions-{$this->getOwnerRecord()->name}-")
                                    ->append(now()->format('Y-m-d-Hisv'))
                                    ->slug();
                            })
                            ->withWriterType(Excel::CSV)
                            ->withColumns([
                                Column::make('id'),
                                Column::make('form_id'),
                                Column::make('content')
                                    ->formatStateUsing(function ($state, $record) {
                                        return json_encode($record->content);
                                    }),
                                Column::make('created_at'),
                                Column::make('updated_at'),
                            ])
                            ->withNamesAsHeadings(),
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
