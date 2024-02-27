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

namespace AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages;

use Filament\Tables\Table;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use AdvisingApp\Prospect\Models\Prospect;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Tables\Actions\DeleteAction;
use AdvisingApp\Form\Models\FormSubmission;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\Form\Enums\FormSubmissionStatus;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\Form\Filament\Resources\FormResource;
use AdvisingApp\Form\Filament\Actions\RequestFormSubmission;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\Form\Filament\Tables\Filters\FormSubmissionStatusFilter;

class ManageProspectFormSubmissions extends ManageRelatedRecords
{
    protected static string $resource = ProspectResource::class;

    protected static string $relationship = 'formSubmissions';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $navigationLabel = 'Form Submissions';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $breadcrumb = 'Form Submissions';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('submissible.name')
                    ->label('Form')
                    ->searchable()
                    ->url(fn (FormSubmission $record): string => FormResource::getUrl('edit', ['record' => $record->submissible])),
                TextColumn::make('status')
                    ->badge()
                    ->getStateUsing(fn (FormSubmission $record): FormSubmissionStatus => $record->getStatus()),
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('requester.name'),
                TextColumn::make('requested_at')
                    ->dateTime()
                    ->getStateUsing(fn (FormSubmission $record): ?CarbonInterface => $record->requester ? $record->created_at : null),
            ])
            ->filters([
                FormSubmissionStatusFilter::make(),
            ])
            ->headerActions([
                RequestFormSubmission::make(),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading(fn (FormSubmission $record) => 'Submission Details: ' . $record->submitted_at->format('M j, Y H:i:s'))
                    ->modalContent(fn (FormSubmission $record) => view('form::submission', ['submission' => $record]))
                    ->visible(fn (FormSubmission $record) => $record->submitted_at),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationItems(array $urlParameters = []): array
    {
        $item = parent::getNavigationItems($urlParameters)[0];

        $ownerRecord = $urlParameters['record'];

        /** @var Prospect $ownerRecord */
        $formSubmissionsCount = Cache::tags('form-submission-count')
            ->remember(
                "form-submission-count-{$ownerRecord->getKey()}",
                now()->addMinutes(5),
                function () use ($ownerRecord): int {
                    return $ownerRecord->formSubmissions()->count();
                },
            );

        $item->badge($formSubmissionsCount > 0 ? $formSubmissionsCount : null);

        return [$item];
    }
}
