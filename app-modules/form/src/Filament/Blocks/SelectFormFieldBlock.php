<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Filament\Forms\Components\KeyValue;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\TextEntry;

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

    public function fields(): array
    {
        return [
            KeyValue::make('options')
                ->keyLabel('Value')
                ->valueLabel('Label'),
        ];
    }

    public static function getInfolistEntry(FormField $field): Entry
    {
        return TextEntry::make($field->key)
            ->label($field->label)
            ->formatStateUsing(function ($state) use ($field) {
                return $field->config['options'][$state] ?? $state;
            });
    }
}
