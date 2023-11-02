<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Filament\Forms\Components\KeyValue;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\TextEntry;

class RadioFormFieldBlock extends FormFieldBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Radio');
    }

    public static function type(): string
    {
        return 'radio';
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

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'radio',
            'label' => $field->label,
            'name' => $field->key,
            ...($field->required ? ['validation' => 'required'] : []),
            'options' => $field->config['options'],
        ];
    }

    public static function getValidationRules(FormField $field): array
    {
        return [
            'string',
            'in:' . collect($field->config['options'])->keys()->join(','),
        ];
    }
}
