<?php

namespace Assist\Alert\Filament\Resources\AlertResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Assist\Alert\Models\Alert;
use Filament\Infolists\Infolist;
use App\Filament\Columns\IdColumn;
use Assist\Prospect\Models\Prospect;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Alert\Filament\Resources\AlertResource;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectAlerts;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ManageStudentAlerts;

class ListAlerts extends ListRecords
{
    protected static string $resource = AlertResource::class;

    // TODO: Change this to a link to the students page when tableAction link triggering becomes available in Filament 3.1
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('concern.display_name')
                    ->label('Related To')
                    ->getStateUsing(fn (Alert $record): ?string => $record->concern?->{$record->concern::displayNameKey()})
                    ->url(fn (Alert $record) => match ($record->concern ? $record->concern::class : null) {
                        Student::class => ManageStudentAlerts::getUrl(['record' => $record->concern]),
                        Prospect::class => ManageProspectAlerts::getUrl(['record' => $record->concern]),
                        default => null,
                    }),
                TextEntry::make('description'),
                TextEntry::make('severity'),
                TextEntry::make('suggested_intervention'),
                TextEntry::make('status'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('concern.display_name')
                    ->label('Related To')
                    ->getStateUsing(fn (Alert $record): ?string => $record->concern?->{$record->concern::displayNameKey()})
                    ->url(fn (Alert $record) => match ($record->concern ? $record->concern::class : null) {
                        Student::class => ManageStudentAlerts::getUrl(['record' => $record->concern]),
                        Prospect::class => ManageProspectAlerts::getUrl(['record' => $record->concern]),
                        default => null,
                    })
                    ->searchable(query: fn (Builder $query, $search) => $query->educatableSearch(relationship: 'concern', search: $search))
                    ->forceSearchCaseInsensitive()
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(),
                TextColumn::make('severity')
                    ->sortable(),
                TextColumn::make('status')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
