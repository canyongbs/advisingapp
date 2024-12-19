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

namespace AdvisingApp\Application\Filament\Resources\ApplicationResource\Pages;

use AdvisingApp\Application\Enums\ApplicationSubmissionStateClassification;
use AdvisingApp\Application\Exports\ApplicationSubmissionExport;
use AdvisingApp\Application\Filament\Resources\ApplicationResource;
use AdvisingApp\Application\Filament\Resources\ApplicationResource\Actions\ApplicationAdmissionActions;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Models\Scopes\ClassifiedAs;
use App\Filament\Tables\Columns\IdColumn;
use App\Filament\Tables\Filters\OpenSearch\SelectFilter;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class ManageApplicationSubmissions extends ManageRelatedRecords
{
    protected static string $resource = ApplicationResource::class;

    // TODO: Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'submissions';

    protected static ?string $navigationLabel = 'Submissions';

    protected static ?string $breadcrumb = 'Submissions';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public function getDefaultActiveTab(): string | int | null
    {
        return 'received';
    }

    public function getTabs(): array
    {
        return [
            'received' => Tab::make('Received')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('state', fn (Builder $query) => $query->tap(new ClassifiedAs(ApplicationSubmissionStateClassification::Received)))),
            'review' => Tab::make('Review')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('state', fn (Builder $query) => $query->tap(new ClassifiedAs(ApplicationSubmissionStateClassification::Review)))),
            'documents_required' => Tab::make('Documents Required')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('state', fn (Builder $query) => $query->tap(new ClassifiedAs(ApplicationSubmissionStateClassification::DocumentsRequired)))),
            'complete' => Tab::make('Complete')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('state', fn (Builder $query) => $query->tap(new ClassifiedAs(ApplicationSubmissionStateClassification::Complete)))),
            'admit' => Tab::make('Admit')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('state', fn (Builder $query) => $query->tap(new ClassifiedAs(ApplicationSubmissionStateClassification::Admit)))),
            'deny' => Tab::make('Deny')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('state', fn (Builder $query) => $query->tap(new ClassifiedAs(ApplicationSubmissionStateClassification::Deny)))),
            'all' => Tab::make('All'),
        ];
    }

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
                TextColumn::make('state')
                    ->badge()
                    ->state(function (ApplicationSubmission $record) {
                        return $record->state->name;
                    })
                    ->color(function (ApplicationSubmission $record) {
                        return $record->state->color->value;
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->join('application_submission_states', 'application_submissions.status_id', '=', 'application_submission_states.id')
                            ->orderBy('application_submission_states.name', $direction);
                    }),
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
                        $filename = str("application-submissions-{$this->getOwnerRecord()->name}-")
                            ->append(now()->format('Y-m-d-Hisv'))
                            ->slug()
                            ->append('.csv');

                        return Excel::download(new ApplicationSubmissionExport($this->getOwnerRecord()->submissions), $filename);
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading(fn (ApplicationSubmission $record) => "Submission Details: {$record->created_at}")
                    ->infolist(fn (ApplicationSubmission $record): array => [
                        TextEntry::make('state')
                            ->label('State')
                            ->badge()
                            ->state(function (ApplicationSubmission $record) {
                                return $record->state->name;
                            })
                            ->color(function (ApplicationSubmission $record) {
                                return $record->state->color->value;
                            }),
                        Section::make('Authenticated author')
                            ->schema([
                                TextEntry::make('author.' . $record->author::displayNameKey())
                                    ->label('Name'),
                                TextEntry::make('author.email')
                                    ->label('Email address'),
                            ])
                            ->columns(2),
                    ])
                    ->modalContent(
                        fn (ApplicationSubmission $record) => view('application::submission', ['submission' => $record])
                    )
                    ->extraModalFooterActions(ApplicationAdmissionActions::get()),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('Export')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $filename = str("selected-application-submissions-{$this->getOwnerRecord()->name}-")
                                ->append(now()->format('Y-m-d-Hisv'))
                                ->slug()
                                ->append('.csv');

                            return Excel::download(new ApplicationSubmissionExport($records), $filename);
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
