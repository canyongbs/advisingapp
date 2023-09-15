<?php

namespace App\Filament\Widgets;

use Filament\Tables\Table;
use Assist\Prospect\Models\Prospect;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentLeadsList extends BaseWidget
{
    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 2,
        'lg' => 4,
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
            ->heading('Recent Prospects')
            ->query(
                Prospect::latest()->limit(10)
            )
            ->columns([
                TextColumn::make('id')
                    ->hidden(),
                TextColumn::make('display_name')
                    ->label('Name')
                    ->getStateUsing(fn (Prospect $record) => $record->displayName()),
                TextColumn::make('email')
                    ->translateLabel(),
                TextColumn::make('mobile')
                    ->translateLabel(),
                TextColumn::make('status')
                    ->badge()
                    ->translateLabel()
                    ->state(function (Prospect $record) {
                        return $record->status->name;
                    })
                    ->color(function (Prospect $record) {
                        return $record->status->color;
                    }),
                TextColumn::make('source.name')
                    ->label('Source')
                    ->translateLabel(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->translateLabel()
                    ->dateTime('g:ia - M j, Y '),
            ])
            ->actions([
                ViewAction::make()
                    ->url(fn (Prospect $record): string => route('filament.admin.resources.prospects.view', $record)),
            ])
            ->recordUrl(
                fn (Prospect $record): string => route('filament.admin.resources.prospects.view', ['record' => $record]),
            )
            ->paginated(false);
    }
}
