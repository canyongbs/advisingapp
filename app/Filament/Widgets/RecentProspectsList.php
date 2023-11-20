<?php

namespace App\Filament\Widgets;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Assist\Prospect\Models\Prospect;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Assist\Prospect\Filament\Resources\ProspectResource;

class RecentProspectsList extends BaseWidget
{
    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 1,
        'lg' => 2,
    ];

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 4,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest Prospects (5)')
            ->query(
                Prospect::latest()->limit(5)
            )
            ->columns([
                IdColumn::make(),
                TextColumn::make(Prospect::displayNameKey())
                    ->label('Name'),
                TextColumn::make('email'),
                TextColumn::make('mobile')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->badge()
                    ->state(function (Prospect $record) {
                        return $record->status->name;
                    })
                    ->color(function (Prospect $record) {
                        return $record->status->color->value;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('source.name')
                    ->label('Source')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('g:ia - M j, Y ')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                ViewAction::make()
                    ->url(fn (Prospect $record): string => ProspectResource::getUrl(name: 'view', parameters: ['record' => $record])),
            ])
            ->recordUrl(
                fn (Prospect $record): string => ProspectResource::getUrl(name: 'view', parameters: ['record' => $record]),
            )
            ->paginated(false);
    }
}
