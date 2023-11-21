<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Assist\Prospect\Models\Prospect;
use Illuminate\Support\Facades\Cache;
use Assist\Form\Models\FormSubmission;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Form\Filament\Resources\FormResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Assist\AssistDataModel\Filament\Resources\StudentResource;

class ManageStudentFormSubmissions extends ManageRelatedRecords
{
    protected static string $resource = StudentResource::class;

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
                TextColumn::make('form.name')
                    ->searchable()
                    ->url(fn (FormSubmission $record): string => FormResource::getUrl('edit', ['record' => $record->form])),
                TextColumn::make('created_at')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading(fn (FormSubmission $record) => "Submission Details: {$record->created_at}")
                    ->modalContent(fn (FormSubmission $record) => view('form::submission', ['submission' => $record])),
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
