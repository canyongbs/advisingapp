<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\TextEntry;

class TextInputFormFieldBlock extends FormFieldBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Text Input');
    }

    public static function type(): string
    {
        return 'text_input';
    }

    public function fields(): array
    {
        return [];
    }

    public static function getInfolistEntry(FormField $field): Entry
    {
        return TextEntry::make($field->key)
            ->label($field->label);
    }
}
