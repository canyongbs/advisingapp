<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select as FilamentSelect;

class SelectFormFieldBlock extends FormFieldBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Select');
    }

    public static function type(): string
    {
        return 'select';
    }

    public static function display(FormField $field): FilamentSelect
    {
        return FilamentSelect::make($field->key)
            ->label($field->label)
            ->required($field->required)
            ->options($field->config['options']);
    }

    public function fields(): array
    {
        return [
            KeyValue::make('options'),
        ];
    }
}
