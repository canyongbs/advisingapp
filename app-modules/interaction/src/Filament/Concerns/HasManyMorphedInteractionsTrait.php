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
use AdvisingApp\Interaction\Settings\InteractionManagementSettings;
use AdvisingApp\Prospect\Models\Prospect;
use App\Features\InteractionMetadataFeature;
use Carbon\CarbonInterface;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

trait HasManyMorphedInteractionsTrait
{
    private ?InteractionManagementSettings $settings = null;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                        TextEntry::make('initiative.name')
                            ->visible(fn () => InteractionMetadataFeature::active() ? $this->getSettings()->is_initiative_enabled : true),
                        TextEntry::make('driver.name')
                            ->visible(fn () => InteractionMetadataFeature::active() ? $this->getSettings()->is_driver_enabled : true),
                        TextEntry::make('division.name'),
                        TextEntry::make('outcome.name')
                            ->visible(fn () => InteractionMetadataFeature::active() ? $this->getSettings()->is_outcome_enabled : true),
                        TextEntry::make('relation.name')
                            ->visible(fn () => InteractionMetadataFeature::active() ? $this->getSettings()->is_relation_enabled : true),
                        TextEntry::make('status.name')
                            ->visible(fn () => InteractionMetadataFeature::active() ? $this->getSettings()->is_status_enabled : true),
                        TextEntry::make('type.name')
                            ->visible(fn () => InteractionMetadataFeature::active() ? $this->getSettings()->is_type_enabled : true),
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
                        TextEntry::make('subject')
                            ->hidden(fn ($state): bool => blank($state))
                            ->columnSpanFull(),
                        TextEntry::make('description')
                            ->state(fn (Interaction $interaction): string => $interaction->description ?? 'N/A')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('end_datetime', 'desc')
            ->columns([
                TextColumn::make('subject')
                    ->description(function (Interaction $record) {
                        if (InteractionMetadataFeature::active()) {
                            return collect([
                                $this->getSettings()->is_initiative_enabled ? $record->initiative?->name : null,
                                ($this->getSettings()->is_driver_enabled && $record->driver?->name) ? '(' . $record->driver->name . ')' : null,
                            ])->filter()->implode(' ');
                        }

                        return $record->initiative?->name . ' (' . $record->driver?->name . ')';
                    })
                    ->icon(fn ($record) => $record->is_confidential ? 'heroicon-m-lock-closed' : null)
                    ->tooltip(fn ($record) => $record->is_confidential ? 'Confidential' : null),
                TextColumn::make('type.name')
                    ->label('Type')
                    ->toggleable()
                    ->visible(fn () => InteractionMetadataFeature::active() ? $this->getSettings()->is_type_enabled : true)
                    ->sortable(),
                TextColumn::make('status.name')
                    ->label('Status')
                    ->toggleable()
                    ->visible(fn () => InteractionMetadataFeature::active() ? $this->getSettings()->is_status_enabled : true)
                    ->sortable(),
                TextColumn::make('start_datetime')
                    ->label('Start Time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('duration')
                    ->state(fn ($record) => $record->end_datetime ? $record->end_datetime->diffForHumans($record->start_datetime, CarbonInterface::DIFF_ABSOLUTE, true, 6) : '-')
                    ->label('Duration'),
                TextColumn::make('user.name')
                    ->label('Created By')
                    ->description(fn ($record) => $record->user?->job_title)
                    ->sortable(),
                TextColumn::make('initiative.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn () => InteractionMetadataFeature::active() ? $this->getSettings()->is_initiative_enabled : true),
                TextColumn::make('driver.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn () => InteractionMetadataFeature::active() ? $this->getSettings()->is_driver_enabled : true),
                TextColumn::make('division.name')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('outcome.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn () => InteractionMetadataFeature::active() ? $this->getSettings()->is_outcome_enabled : true),
                TextColumn::make('relation.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn () => InteractionMetadataFeature::active() ? $this->getSettings()->is_relation_enabled : true),
            ])
            ->headerActions([
                CreateAction::make()
                    ->authorize(function () {
                        $ownerRecord = $this->getOwnerRecord();

                        return auth()->user()?->can('create', [Interaction::class, $ownerRecord instanceof Prospect ? $ownerRecord : null]);
                    }),
            ])
            ->recordActions([
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
            ->filters([
                SelectFilter::make('interaction_initiative_id')
                    ->relationship('initiative', 'name')
                    ->label('Initiative')
                    ->multiple()
                    ->visible(fn () => InteractionMetadataFeature::active() ? $this->getSettings()->is_initiative_enabled : true),
                SelectFilter::make('interaction_driver_id')
                    ->relationship('driver', 'name')
                    ->label('Driver')
                    ->multiple()
                    ->visible(fn () => InteractionMetadataFeature::active() ? $this->getSettings()->is_driver_enabled : true),
                SelectFilter::make('interaction_type_id')
                    ->label('Type')
                    ->relationship('type', 'name')
                    ->multiple()
                    ->visible(fn () => InteractionMetadataFeature::active() ? $this->getSettings()->is_type_enabled : true),
                SelectFilter::make('interaction_status_id')
                    ->relationship('status', 'name')
                    ->label('Status')
                    ->multiple()
                    ->visible(fn () => InteractionMetadataFeature::active() ? $this->getSettings()->is_status_enabled : true),
                SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Created By')
                    ->multiple(),
            ])
            ->emptyStateDescription('Create an interaction to get started');
    }

    private function getSettings(): InteractionManagementSettings
    {
        if ($this->settings === null) {
            $this->settings = app(InteractionManagementSettings::class);
        }

        return $this->settings;
    }
}
