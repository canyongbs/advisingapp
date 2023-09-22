<?php

namespace Assist\Division\Filament\Resources\DivisionResource\Pages;

use Filament\Forms\Form;
use App\Filament\Fields\TiptapEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Assist\Division\Filament\Resources\DivisionResource;

class CreateDivision extends CreateRecord
{
    protected static string $resource = DivisionResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->string()
                    ->unique(),
                Textarea::make('code')
                    ->required()
                    ->string()
                    ->unique(),
                TiptapEditor::make('header')
                    ->string()
                    ->columnSpanFull(),
                TiptapEditor::make('footer')
                    ->string()
                    ->columnSpanFull(),
            ]);
    }
}
