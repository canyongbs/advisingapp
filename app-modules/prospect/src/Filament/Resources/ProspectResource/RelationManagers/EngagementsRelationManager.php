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

namespace Assist\Prospect\Filament\Resources\ProspectResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use App\Filament\Columns\IdColumn;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Assist\Engagement\Models\Engagement;
use Filament\Forms\Components\Component;
use Filament\Tables\Actions\CreateAction;
use Filament\Infolists\Components\Fieldset;
use Filament\Forms\Components\MorphToSelect;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Assist\Engagement\Enums\EngagementDeliveryStatus;
use App\Filament\Resources\RelationManagers\RelationManager;
use Assist\Engagement\Actions\CreateDeliverablesForEngagement;
use Assist\Engagement\Filament\Resources\EngagementResource\Pages\CreateEngagement;

class EngagementsRelationManager extends RelationManager
{
    protected static string $relationship = 'engagements';

    public function form(Form $form): Form
    {
        $createEngagementForm = (resolve(CreateEngagement::class))->form($form);

        $formComponents = collect($createEngagementForm->getComponents())->filter(function (Component $component) {
            if (! $component instanceof MorphToSelect) {
                return true;
            }
        })->toArray();

        return $createEngagementForm
            ->schema([
                Hidden::make('recipient_id')
                    ->default($this->getOwnerRecord()->identifier()),
                Hidden::make('recipient_type')
                    ->default(resolve(Prospect::class)->getMorphClass()),
                ...$formComponents,
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('user.name')
                    ->label('Created By'),
                Fieldset::make('Content')
                    ->schema([
                        TextEntry::make('subject'),
                        TextEntry::make('body'),
                    ]),
                RepeatableEntry::make('deliverables')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('channel'),
                        IconEntry::make('delivery_status')
                            ->icon(fn (EngagementDeliveryStatus $state): string => match ($state) {
                                EngagementDeliveryStatus::Successful => 'heroicon-o-check-circle',
                                EngagementDeliveryStatus::Awaiting => 'heroicon-o-clock',
                                EngagementDeliveryStatus::Failed => 'heroicon-o-x-circle',
                            })
                            ->color(fn (EngagementDeliveryStatus $state): string => match ($state) {
                                EngagementDeliveryStatus::Successful => 'success',
                                EngagementDeliveryStatus::Awaiting => 'info',
                                EngagementDeliveryStatus::Failed => 'danger',
                            }),
                        TextEntry::make('delivered_at'),
                        TextEntry::make('delivery_response'),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                IdColumn::make(),
                TextColumn::make('subject'),
                TextColumn::make('body'),
                TextColumn::make('channels')
                    ->label('Delivery Channels')
                    ->state(function (Engagement $record) {
                        return $record->deliverables->pluck('channel')->map(function ($channel) {
                            return $channel->name;
                        })->implode(', ');
                    }),
            ])
            ->filters([
            ])
            ->headerActions([
                CreateAction::make()
                    ->after(function (Engagement $engagement, array $data) {
                        $this->afterCreate($engagement, $data['delivery_methods']);
                    }),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
            ]);
    }

    public function afterCreate(Engagement $engagement, array $deliveryMethods): void
    {
        $createDeliverablesForEngagement = resolve(CreateDeliverablesForEngagement::class);

        $createDeliverablesForEngagement($engagement, $deliveryMethods);
    }
}
