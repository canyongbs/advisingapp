<?php

namespace AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceSourceResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceSourceResource;

class EditAnalyticsResourceSource extends EditRecord
{
    protected static string $resource = AnalyticsResourceSourceResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->string()
                    ->unique(ignoreRecord: true),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
