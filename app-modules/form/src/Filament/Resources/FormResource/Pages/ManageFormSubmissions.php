<?php

namespace Assist\Form\Filament\Resources\FormResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use Assist\Form\Models\FormSubmission;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Assist\Form\Exports\FormSubmissionExport;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Form\Filament\Resources\FormResource;
use App\Filament\Filters\OpenSearch\SelectFilter;
use Filament\Resources\Pages\ManageRelatedRecords;

class ManageFormSubmissions extends ManageRelatedRecords
{
    protected static string $resource = FormResource::class;

    // TODO: Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'submissions';

    protected static ?string $navigationLabel = 'Submissions';

    protected static ?string $breadcrumb = 'Submissions';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('author.email')
                    ->searchable(),
                TextColumn::make('author_type')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): ?string => filled($state) ? ucfirst($state) : null)
                    ->color('success'),
            ])
            ->filters([
                SelectFilter::make('author_type')
                    ->options([
                        'student' => 'Student',
                        'prospect' => 'Prospect',
                    ]),
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
                    ->modalHeading(fn (FormSubmission $record) => "Submission Details: {$record->created_at}")
                    ->modalContent(fn (FormSubmission $record) => view('form::submission', ['submission' => $record])),
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
