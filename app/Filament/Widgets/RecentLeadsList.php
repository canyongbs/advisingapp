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
            ->heading('Recent Leads')
            ->query(
                Prospect::latest()->limit(10)
            )
            ->columns([
                TextColumn::make('full')
                    ->label('Name')
                    ->translateLabel(),
                TextColumn::make('email')
                    ->translateLabel(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->translateLabel()
                    ->dateTime('g:ia - M j, Y '),
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }
}
