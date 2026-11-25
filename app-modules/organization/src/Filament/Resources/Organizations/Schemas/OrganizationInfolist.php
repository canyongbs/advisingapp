<?php

namespace AdvisingApp\Organization\Filament\Resources\Organizations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrganizationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Details')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')
                            ->placeholder('-'),
                        TextEntry::make('phone_number')
                            ->label('Phone Number')
                            ->placeholder('-'),
                        TextEntry::make('website')
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Address')
                    ->schema([
                        TextEntry::make('address')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('city')
                            ->placeholder('-'),
                        TextEntry::make('state')
                            ->placeholder('-'),
                        TextEntry::make('postalcode')
                            ->label('Postal Code')
                            ->placeholder('-'),
                        TextEntry::make('country')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
