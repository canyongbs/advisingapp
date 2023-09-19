<?php

namespace Assist\Division\Filament\Resources\DivisionResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\DeleteAction;
use App\Filament\Fields\TiptapEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Assist\Division\Filament\Resources\DivisionResource;

class EditDivision extends EditRecord
{
    protected static string $resource = DivisionResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->string()
                    ->unique(ignoreRecord: true),
                Textarea::make('code')
                    ->required()
                    ->string()
                    ->unique(ignoreRecord: true),
                TiptapEditor::make('header')
                    ->string()
                    ->columnSpanFull(),
                TiptapEditor::make('footer')
                    ->string()
                    ->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
