<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use App\Models\User;
use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\UserResource;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Assist\Prospect\Filament\Resources\ProspectResource;

class ManageProspectSubscriptions extends ManageRelatedRecords
{
    protected static string $resource = ProspectResource::class;

    protected static string $relationship = 'subscribedUsers';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $navigationLabel = 'Subscriptions';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $breadcrumb = 'Subscriptions';

    protected static ?string $navigationIcon = 'heroicon-o-user';

    //TODO: manually override check canAccess for policy

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->url(fn ($record) => UserResource::getUrl('view', ['record' => $record]))
                    ->color('primary'),
                TextColumn::make('pivot.created_at')
                    ->label('Subscribed At'),
            ])
            ->filters([
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Create Subscription')
                    ->modalHeading(function () {
                        /** @var Prospect $prospect */
                        $prospect = $this->getOwnerRecord();

                        return 'Subscribe a User to ' . $prospect->display_name;
                    })
                    ->modalSubmitActionLabel('Subscribe')
                    ->attachAnother(false)
                    ->color('primary')
                    ->recordSelect(
                        fn (Select $select) => $select->placeholder('Select a User'),
                    )
                    ->successNotificationTitle(function (User $record) {
                        /** @var Prospect $prospect */
                        $prospect = $this->getOwnerRecord();

                        return "{$record->name} was subscribed to {$prospect->display_name}";
                    }),
            ])
            ->actions([
                DetachAction::make()
                    ->label('Unsubscribe')
                    ->modalHeading(function (User $record) {
                        /** @var Prospect $prospect */
                        $prospect = $this->getOwnerRecord();

                        return "Unsubscribe {$record->name} from {$prospect->display_name}";
                    })
                    ->modalSubmitActionLabel('Unsubscribe')
                    ->successNotificationTitle(function (User $record) {
                        /** @var Prospect $prospect */
                        $prospect = $this->getOwnerRecord();

                        return "{$record->name} was unsubscribed from {$prospect->display_name}";
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->label('Unsubscribe selected')
                        ->modalHeading(function () {
                            /** @var Prospect $prospect */
                            $prospect = $this->getOwnerRecord();

                            return "Unsubscribe selected from {$prospect->display_name}";
                        })
                        ->modalSubmitActionLabel('Unsubscribe')
                        ->successNotificationTitle(function () {
                            /** @var Prospect $prospect */
                            $prospect = $this->getOwnerRecord();

                            return "All selected were unsubscribed from {$prospect->display_name}";
                        }),
                ]),
            ])
            ->emptyStateHeading('No Subscriptions')
            ->inverseRelationship('prospectSubscriptions');
    }
}
