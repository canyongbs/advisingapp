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
                    ->string()
                    ->required(),
                TextInput::make('description')
                    ->columnSpanFull()
                    ->string(),
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
