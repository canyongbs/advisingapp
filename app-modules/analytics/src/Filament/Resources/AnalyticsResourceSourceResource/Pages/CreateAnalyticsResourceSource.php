<?php

namespace AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceSourceResource\Pages;

use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceSourceResource;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

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
