<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Filament\Forms\Components\Textarea as FilamentTextArea;

class TextArea extends Block
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Text Area');
    }

    public static function make(string $name = 'text_area'): static
    {
        return parent::make($name);
    }

    public static function display(FormField $field): FilamentTextArea
    {
        return FilamentTextArea::make($field->key)
            ->label($field->label)
            ->required($field->required);
    }

    public function fields(): array
    {
        return [];
    }
}
