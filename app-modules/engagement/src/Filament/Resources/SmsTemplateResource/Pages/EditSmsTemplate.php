<?php

namespace Assist\Engagement\Filament\Resources\SmsTemplateResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\RichEditor;
use Assist\Engagement\Filament\Resources\SmsTemplateResource;

class EditSmsTemplate extends EditRecord
{
    protected static string $resource = SmsTemplateResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->string()
                    ->required()
                    ->autocomplete(false),
                TextInput::make('description')
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

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
