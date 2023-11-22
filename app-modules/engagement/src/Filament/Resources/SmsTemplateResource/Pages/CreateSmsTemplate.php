<?php

namespace Assist\Engagement\Filament\Resources\SmsTemplateResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Resources\Pages\CreateRecord;
use Assist\Engagement\Filament\Resources\SmsTemplateResource;

class CreateSmsTemplate extends CreateRecord
{
    protected static string $resource = SmsTemplateResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->columnSpan('1/4')
                    ->string()
                    ->required()
                    ->autocomplete(false),
                TextInput::make('description')
                    ->columnSpanFull()
                    ->string()
                    ->autocomplete(false),
                RichEditor::make('content')
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'link',
                        'redo',
                        'undo',
                    ])
                    ->maxLength(320)
                    ->required(),
            ]);
    }
}
