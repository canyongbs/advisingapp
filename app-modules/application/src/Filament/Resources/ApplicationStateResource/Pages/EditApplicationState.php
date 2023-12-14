<?php

namespace AdvisingApp\Application\Filament\Resources\ApplicationStateResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use AdvisingApp\Application\Enums\ApplicationStateColorOptions;
use AdvisingApp\Application\Enums\ApplicationStateClassification;
use AdvisingApp\Application\Filament\Resources\ApplicationStateResource;

class EditApplicationState extends EditRecord
{
    protected static string $resource = ApplicationStateResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->string(),
                Select::make('classification')
                    ->label('Classification')
                    ->searchable()
                    ->options(ApplicationStateClassification::class)
                    ->required()
                    ->enum(ApplicationStateClassification::class),
                Select::make('color')
                    ->label('Color')
                    ->searchable()
                    ->options(ApplicationStateColorOptions::class)
                    ->required()
                    ->enum(ApplicationStateColorOptions::class),
                Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->string(),
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
