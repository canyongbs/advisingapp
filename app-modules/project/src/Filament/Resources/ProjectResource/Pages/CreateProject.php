<?php

namespace AdvisingApp\Project\Filament\Resources\ProjectResource\Pages;

use AdvisingApp\Project\Filament\Resources\ProjectResource;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
  protected static string $resource = ProjectResource::class;

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        TextInput::make('name')
          ->required()
          ->unique()
          ->string()
          ->maxLength(255),
        Textarea::make('description')
          ->string()
          ->maxLength(65535)
          ->columnSpanFull(),
      ]);
  }
}
