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

namespace AdvisingApp\Form\Filament\Resources\FormResource\Pages;

use AdvisingApp\Form\Enums\FormSubmissionStatus;
use AdvisingApp\Form\Exports\FormSubmissionExport;
use AdvisingApp\Form\Filament\Resources\FormResource;
use AdvisingApp\Form\Filament\Tables\Filters\FormSubmissionStatusFilter;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormSubmission;
use App\Filament\Tables\Columns\IdColumn;
use App\Filament\Tables\Filters\OpenSearch\SelectFilter;
use Carbon\CarbonInterface;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

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
                TextColumn::make('status')
                    ->badge()
                    ->getStateUsing(fn (FormSubmission $record): FormSubmissionStatus => $record->getStatus()),
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('author.email')
                    ->searchable(),
                TextColumn::make('author_type')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): ?string => filled($state) ? ucfirst($state) : null)
                    ->color('success'),
                TextColumn::make('requester.name'),
                TextColumn::make('requested_at')
                    ->dateTime()
                    ->getStateUsing(fn (FormSubmission $record): ?CarbonInterface => $record->requester ? $record->created_at : null),
            ])
            ->filters([
                FormSubmissionStatusFilter::make(),
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
                    ->modalHeading(fn (FormSubmission $record) => 'Submission Details: ' . $record->submitted_at->format('M j, Y H:i:s'))
                    ->infolist(fn (FormSubmission $record): ?array => ($record->author && $record->submissible->is_authenticated) ? [
                        Section::make('Authenticated author')
                            ->schema([
                                TextEntry::make('author.' . $record->author::displayNameKey())
                                    ->label('Name'),
                                TextEntry::make('author.email')
                                    ->label('Email address'),
                            ])
                            ->columns(2),
                    ] : null)
                    ->modalContent(fn (FormSubmission $record) => view('form::submission', ['submission' => $record]))
                    ->visible(fn (FormSubmission $record) => $record->submitted_at),
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

    public static function getNavigationItems(array $urlParameters = []): array
    {
        $item = parent::getNavigationItems($urlParameters)[0];

        $ownerRecord = $urlParameters['record'];

        /** @var Form $ownerRecord */
        $formSubmissionsCount = Cache::tags('form-submission-count')
            ->remember(
                "form-submission-count-{$ownerRecord->getKey()}",
                now()->addMinutes(5),
                fn (): int => $ownerRecord->submissions()->count(),
            );

        $item->badge($formSubmissionsCount);

        return [$item];
    }
}
