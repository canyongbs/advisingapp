<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\TextEntry;

class UrlFormFieldBlock extends FormFieldBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('URL');
    }

    public static function type(): string
    {
        return 'url';
    }

    public function fields(): array
    {
        return [];
    }

    public static function getInfolistEntry(FormField $field): Entry
    {
        return TextEntry::make($field->id)
            ->label($field->label)
            ->url(fn (Model $record): ?string => $record->getAttributeValue($field->id));
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'url',
            'label' => $field->label,
            'name' => $field->id,
            ...($field->is_required ? ['validation' => 'required'] : []),
        ];
    }

    public static function getValidationRules(FormField $field): array
    {
        return ['url', 'max:255'];
    }
}
