<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\TextEntry;

class TextAreaFormFieldBlock extends FormFieldBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Text Area');
    }

    public static function type(): string
    {
        return 'text_area';
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
