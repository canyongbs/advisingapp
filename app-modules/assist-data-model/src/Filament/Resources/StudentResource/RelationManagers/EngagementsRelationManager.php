<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use App\Filament\Columns\IdColumn;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Assist\Engagement\Models\Engagement;
use Filament\Forms\Components\Component;
use Filament\Tables\Actions\CreateAction;
use Assist\AssistDataModel\Models\Student;
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
                    ->default(resolve(Student::class)->getMorphClass()),
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
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->after(function (Engagement $engagement, array $data) {
                        $this->afterCreate($engagement, $data['delivery_methods']);
                    }),
            ]);
    }

    public function afterCreate(Engagement $engagement, array $deliveryMethods): void
    {
        $createDeliverablesForEngagement = resolve(CreateDeliverablesForEngagement::class);

        $createDeliverablesForEngagement($engagement, $deliveryMethods);
    }
}
