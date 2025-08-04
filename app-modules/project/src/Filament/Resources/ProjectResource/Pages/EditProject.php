<?php

namespace AdvisingApp\Project\Filament\Resources\ProjectResource\Pages;

use AdvisingApp\Project\Filament\Resources\ProjectResource;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
  use EditPageRedirection;

  protected static string $resource = ProjectResource::class;

  protected static ?string $navigationLabel = 'Edit';

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        TextInput::make('name')
          ->required()
          ->string()
          ->unique(ignoreRecord: true)
          ->maxLength(255),
        Textarea::make('description')
          ->string()
          ->maxLength(65535)
          ->columnSpanFull(),
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
