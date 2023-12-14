<?php

namespace AdvisingApp\Application\Filament\Resources\ApplicationStateSubmissionResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use AdvisingApp\Application\Enums\ApplicationSubmissionStateColorOptions;
use AdvisingApp\Application\Enums\ApplicationSubmissionStateClassification;
use AdvisingApp\Application\Filament\Resources\ApplicationSubmissionStateResource;

class EditApplicationSubmissionState extends EditRecord
{
    protected static string $resource = ApplicationSubmissionStateResource::class;

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
                    ->options(ApplicationSubmissionStateClassification::class)
                    ->required()
                    ->enum(ApplicationSubmissionStateClassification::class),
                Select::make('color')
                    ->label('Color')
                    ->searchable()
                    ->options(ApplicationSubmissionStateColorOptions::class)
                    ->required()
                    ->enum(ApplicationSubmissionStateColorOptions::class),
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
