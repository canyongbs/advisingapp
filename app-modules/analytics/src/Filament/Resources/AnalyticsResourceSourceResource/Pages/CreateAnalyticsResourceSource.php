<?php

namespace AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceSourceResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceSourceResource;

class CreateAnalyticsResourceSource extends CreateRecord
{
    protected static string $resource = AnalyticsResourceSourceResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->string()
                    ->unique(),
            ]);
    }
}
