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

namespace AdvisingApp\CaseManagement\Filament\Resources\CaseResource\RelationManagers;

use AdvisingApp\CaseManagement\Enums\CaseUpdateDirection;
use AdvisingApp\CaseManagement\Filament\Resources\CaseUpdateResource;
use AdvisingApp\CaseManagement\Models\CaseUpdate;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CaseUpdatesRelationManager extends RelationManager
{
    protected static string $relationship = 'caseUpdates';

    protected static ?string $recordTitleAttribute = 'update';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('update')
                    ->label('Update')
                    ->rows(3)
                    ->columnSpan('full')
                    ->required()
                    ->string(),
                Select::make('direction')
                    ->options(CaseUpdateDirection::class)
                    ->label('Direction')
                    ->required()
                    ->enum(CaseUpdateDirection::class)
                    ->default(CaseUpdateDirection::default()),
                Toggle::make('internal')
                    ->label('Internal')
                    ->rule(['boolean']),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('update')
                    ->label('Update')
                    ->words(6),
                IconColumn::make('internal')
                    ->boolean(),
                TextColumn::make('direction')
                    ->icon(fn (CaseUpdateDirection $state): string => $state->getIcon())
                    ->formatStateUsing(fn (CaseUpdateDirection $state): string => $state->getLabel()),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (CaseUpdate $caseUpdate) => CaseUpdateResource::getUrl('view', ['record' => $caseUpdate])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
