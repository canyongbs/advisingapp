<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\TextEntry;

class NumberFormFieldBlock extends FormFieldBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Number');
    }

    public static function type(): string
    {
        return 'number';
    }

    public function fields(): array
    {
        return [];
    }

    public static function getInfolistEntry(FormField $field): Entry
    {
        return TextEntry::make($field->key)
            ->label($field->label)
            ->numeric();
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'number',
            'label' => $field->label,
            'name' => $field->key,
            ...($field->required ? ['validation' => 'required'] : []),
        ];
    }

    public static function getValidationRules(FormField $field): array
    {
        return ['numeric'];
    }
}
