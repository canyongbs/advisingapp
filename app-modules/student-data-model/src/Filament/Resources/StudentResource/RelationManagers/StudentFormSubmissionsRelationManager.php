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

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers;

use App\Enums\Feature;
use Filament\Tables\Table;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Gate;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Tables\Actions\DeleteAction;
use AdvisingApp\Form\Models\FormSubmission;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\Form\Enums\FormSubmissionStatus;
use AdvisingApp\Form\Filament\Resources\FormResource;
use Filament\Resources\RelationManagers\RelationManager;
use AdvisingApp\Form\Filament\Actions\RequestFormSubmission;
use AdvisingApp\Form\Filament\Tables\Filters\FormSubmissionStatusFilter;

class StudentFormSubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'formSubmissions';

    protected static ?string $title = 'Forms';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return Gate::check(Feature::OnlineForms->getGateName());
    }

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
}
