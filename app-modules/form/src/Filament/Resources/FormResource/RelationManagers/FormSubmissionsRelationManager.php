<?php

namespace Assist\Form\Filament\Resources\FormResource\RelationManagers;

use Excel;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Assist\Form\Models\FormSubmission;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Assist\Form\Exports\FormSubmissionExport;
use Filament\Tables\Actions\DeleteBulkAction;
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
            ->columns([
                IdColumn::make(),
                TextColumn::make('created_at')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->headerActions([
                Action::make('export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        $filename = str("form-submissions-{$this->getOwnerRecord()->name}-")
                            ->append(now()->format('Y-m-d-Hisv'))
                            ->slug()
                            ->append('.csv');

                        return Excel::download(new FormSubmissionExport($this->getOwnerRecord()->submissions), $filename);
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading(fn (FormSubmission $record) => "Submission Details: {$record->created_at}"),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('Export')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $filename = str("selected-form-submissions-{$this->getOwnerRecord()->name}-")
                                ->append(now()->format('Y-m-d-Hisv'))
                                ->slug()
                                ->append('.csv');

                            return Excel::download(new FormSubmissionExport($records), $filename);
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
