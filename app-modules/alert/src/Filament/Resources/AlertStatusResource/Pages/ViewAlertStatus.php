<?php

namespace AdvisingApp\Alert\Filament\Resources\AlertStatusResource\Pages;

use AdvisingApp\Alert\Filament\Resources\AlertStatusResource;
use AdvisingApp\Alert\Models\AlertStatus;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewAlertStatus extends ViewRecord
{
    protected static string $resource = AlertStatusResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Name'),
                        TextInput::make('classification')
                            ->label('Classification'),
                        TextInput::make('sort')
                            ->numeric(),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
