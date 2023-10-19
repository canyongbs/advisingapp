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

class ManageProspectCareTeam extends ManageRelatedRecords
{
    protected static string $resource = ProspectResource::class;

    protected static string $relationship = 'careTeam';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $navigationLabel = 'Care Team';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $breadcrumb = 'Care Team';

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->url(fn ($record) => UserResource::getUrl('view', ['record' => $record]))
                    ->color('primary'),
            ])
            ->filters([
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Add to Care Team')
                    ->modalHeading(function () {
                        /** @var Prospect $prospect */
                        $prospect = $this->getOwnerRecord();

                        return "Add a User to {$prospect->display_name}'s Care Team";
                    })
                    ->modalSubmitActionLabel('Add')
                    ->attachAnother(false)
                    ->color('primary')
                    ->recordSelect(
                        fn (Select $select) => $select->placeholder('Select a User'),
                    )
                    ->successNotificationTitle(function (User $record) {
                        /** @var Prospect $prospect */
                        $prospect = $this->getOwnerRecord();

                        return "{$record->name} was added to {$prospect->display_name}'s Care Team";
                    }),
            ])
            ->actions([
                DetachAction::make()
                    ->label('Remove')
                    ->modalHeading(function (User $record) {
                        /** @var Prospect $prospect */
                        $prospect = $this->getOwnerRecord();

                        return "Remove {$record->name} from {$prospect->display_name}'s Care Team";
                    })
                    ->modalSubmitActionLabel('Remove')
                    ->successNotificationTitle(function (User $record) {
                        /** @var Prospect $prospect */
                        $prospect = $this->getOwnerRecord();

                        return "{$record->name} was removed from {$prospect->display_name}'s Care Team";
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->label('Remove selected')
                        ->modalHeading(function () {
                            /** @var Prospect $prospect */
                            $prospect = $this->getOwnerRecord();

                            return "Remove selected from {$prospect->display_name}'s Care Team";
                        })
                        ->modalSubmitActionLabel('Remove')
                        ->successNotificationTitle(function () {
                            /** @var Prospect $prospect */
                            $prospect = $this->getOwnerRecord();

                            return "All selected were removed from {$prospect->display_name}'s Care Team";
                        }),
                ]),
            ])
            ->emptyStateHeading('No Users')
            ->inverseRelationship('prospectCareTeams');
    }
}
