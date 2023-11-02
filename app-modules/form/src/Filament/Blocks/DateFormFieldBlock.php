<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\TextEntry;

class DateFormFieldBlock extends FormFieldBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Date');
    }

    public static function type(): string
    {
        return 'date';
    }

    public function fields(): array
    {
        return [];
    }

    public static function getInfolistEntry(FormField $field): Entry
    {
        return TextEntry::make($field->key)
            ->label($field->label)
            ->date();
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'date',
            'label' => $field->label,
            'name' => $field->key,
            ...($field->required ? ['validation' => 'required'] : []),
        ];
    }

    public static function getValidationRules(FormField $field): array
    {
        return ['date'];
    }
}
