<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\TextEntry;

class TimeFormFieldBlock extends FormFieldBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Time');
    }

    public static function type(): string
    {
        return 'time';
    }

    public function fields(): array
    {
        return [];
    }

    public static function getInfolistEntry(FormField $field): Entry
    {
        return TextEntry::make($field->key)
            ->label($field->label)
            ->time();
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'time',
            'label' => $field->label,
            'name' => $field->key,
            ...($field->required ? ['validation' => 'required'] : []),
        ];
    }
}
