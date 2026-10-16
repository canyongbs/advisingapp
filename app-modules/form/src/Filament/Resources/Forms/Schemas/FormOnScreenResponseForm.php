<?php

namespace AdvisingApp\Form\Filament\Resources\Forms\Schemas;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Schemas\Schema;

class FormOnScreenResponseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                MarkdownEditor::make('on_screen_response')
                    ->disableToolbarButtons(['attachFiles'])
                    ->columnSpanFull(),
            ]);
    }
}
