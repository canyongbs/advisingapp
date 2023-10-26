<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\ViewEntry;

class SignatureFormFieldBlock extends FormFieldBlock
{
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
        return ViewEntry::make($field->key)
            ->label($field->label)
            ->view('form::infolists.components.signature-entry');
    }
}
