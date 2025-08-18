<?php

namespace AdvisingApp\Project\Filament\Resources\ProjectMilestoneStatusResource\Pages;

use AdvisingApp\Project\Filament\Resources\ProjectMilestoneStatusResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditProjectMilestoneStatus extends EditRecord
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
                    ->unique(ignoreRecord: true),
                Textarea::make('description')
                    ->label('Description')
                    ->maxLength(65535)
                    ->required(),
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
