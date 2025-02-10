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

namespace AdvisingApp\Interaction\Filament\Concerns;

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Prospect\Models\Prospect;
use Carbon\CarbonInterface;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

trait HasManyMorphedInteractionsTrait
{
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('is_confidential')
                    ->columnSpanFull()
                    ->label('')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state ? 'Confidential' : '')
                    ->visible(fn ($record): bool => $record->is_confidential),
                TextEntry::make('user.name')
                    ->label('Created By'),
                Fieldset::make('Details')
                    ->schema([
                        TextEntry::make('initiative.name'),
                        TextEntry::make('driver.name'),
                        TextEntry::make('division.name'),
                        TextEntry::make('outcome.name'),
                        TextEntry::make('relation.name'),
                        TextEntry::make('status.name'),
                        TextEntry::make('type.name'),
                    ]),
                Fieldset::make('Time')
                    ->schema([
                        TextEntry::make('start_datetime')
                            ->label('Start Time')
                            ->dateTime(),
                        TextEntry::make('end_datetime')
                            ->label('End Time')
                            ->dateTime(),
                        TextEntry::make('start_datetime')
                            ->label('Duration')
                            ->state(fn ($record) => $record->end_datetime ? $record->end_datetime->diffForHumans($record->start_datetime, CarbonInterface::DIFF_ABSOLUTE, true, 6) : '-'),
                    ]),
                Fieldset::make('Notes')
                    ->schema([
                        TextEntry::make('subject'),
                        TextEntry::make('description'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('subject')
                    ->icon(fn ($record) => $record->is_confidential ? 'heroicon-m-lock-closed' : null)
                    ->tooltip(fn ($record) => $record->is_confidential ? 'Confidential' : null),
                TextColumn::make('start_datetime')
                    ->label('Start Time')
                    ->dateTime(),
                TextColumn::make('end_datetime')
                    ->label('End Time')
                    ->dateTime(),
                TextColumn::make('created_at')
                    ->state(fn ($record) => $record->end_datetime ? $record->end_datetime->diffForHumans($record->start_datetime, CarbonInterface::DIFF_ABSOLUTE, true, 6) : '-')
                    ->label('Duration'),
                TextColumn::make('initiative.name')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('driver.name')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('division.name')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('outcome.name')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('relation.name')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status.name')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('type.name')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make()
                    ->authorize(function () {
                        $ownerRecord = $this->getOwnerRecord();

                        return auth()->user()->can('create', [Interaction::class, $ownerRecord instanceof Prospect ? $ownerRecord : null]);
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading('Interaction Details')
                    ->extraModalFooterActions([
                        DeleteAction::make()
                            ->modalHeading('Are you sure you wish to delete this interaction?')
                            ->cancelParentActions(),
                    ]),
                EditAction::make()
                    ->modalHeading('Edit Interaction'),
            ])
            ->emptyStateDescription('Create an interaction to get started');
    }
}
