<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\TextEntry;

class PhoneFormFieldBlock extends FormFieldBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Phone number');
    }

    public static function type(): string
    {
        return 'phone';
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

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'tel',
            'label' => $field->label,
            'name' => $field->key,
            ...($field->required ? ['validation' => 'required'] : []),
        ];
    }

    public static function getValidationRules(FormField $field): array
    {
        return ['string', 'max:255'];
    }
}
