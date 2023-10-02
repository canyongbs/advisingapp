<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Filament\Forms\Components\TextInput as FilamentTextInput;

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

    public static function display(FormField $field): FilamentTextInput
    {
        return FilamentTextInput::make($field->key)
            ->label($field->label)
            ->required($field->required);
    }

    public function fields(): array
    {
        return [];
    }
}
