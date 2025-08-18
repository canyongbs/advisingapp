<?php

namespace AdvisingApp\Project\Filament\Resources\ProjectMilestoneStatusResource\Pages;

use AdvisingApp\Project\Filament\Resources\ProjectMilestoneStatusResource;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectMilestoneStatus extends CreateRecord
{
    protected static string $resource = ProjectMilestoneStatusResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->maxLength(255)
                    ->autofocus()
                    ->required()
                    ->string()
                    ->unique(),
                Textarea::make('description')
                    ->label('Description')
                    ->maxLength(65535)
                    ->required(),
            ]);
    }
}
