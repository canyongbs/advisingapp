<?php

namespace AdvisingApp\GroupAppointment\Filament\Resources\BookingGroups\Pages;

use AdvisingApp\GroupAppointment\Filament\Resources\BookingGroups\BookingGroupResource;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class CreateBookingGroup extends CreateRecord
{
    protected static string $resource = BookingGroupResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                    ->required()
                    ->label('Name'),
            Textarea::make('description')
                    ->required()
                    ->columnSpanFull()
                    ->label('Description'),
            Checkbox::make('is_confidential')
                    ->label('Confidential')
                    ->live()
                    ->columnSpanFull(),
            Select::make('user_id')
                    ->label('User')
                    ->relationship('user','name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->visible(fn (Get $get) => $get('is_confidential')),
            Select::make('team_id')
                    ->label('Team')
                    ->relationship('team','name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->visible(fn (Get $get) => $get('is_confidential')),
        ]);
    }
}
