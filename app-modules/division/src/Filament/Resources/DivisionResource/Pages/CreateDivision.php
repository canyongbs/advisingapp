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
                TextInput::make('name'),
                Textarea::make('code'),
                TiptapEditor::make('header')
                    ->columnSpanFull(),
                TiptapEditor::make('footer')
                    ->columnSpanFull(),
            ]);
    }
}
