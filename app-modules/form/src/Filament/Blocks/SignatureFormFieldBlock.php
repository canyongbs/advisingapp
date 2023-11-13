<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\ViewEntry;

class SignatureFormFieldBlock extends FormFieldBlock
{
    public string $preview = 'form::blocks.previews.signature';

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Signature');
    }

    public static function type(): string
    {
        return 'signature';
    }

    public function fields(): array
    {
        return [];
    }

    public static function getInfolistEntry(FormField $field): Entry
    {
        return ViewEntry::make($field->id)
            ->label($field->label)
            ->view('form::infolists.components.signature-entry');
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'signature',
            'label' => $field->label,
            'name' => $field->id,
            ...($field->is_required ? ['validation' => 'required'] : []),
        ];
    }
}
