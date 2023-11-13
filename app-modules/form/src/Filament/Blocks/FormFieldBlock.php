<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Infolists\Components\Entry;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput as FilamentTextInput;
use FilamentTiptapEditor\TiptapBlock;
use Illuminate\Support\Str;

abstract class FormFieldBlock extends TiptapBlock
{
    public string $preview = 'form::blocks.previews.default';

    public function getFormSchema(): array
    {
        return [
            FilamentTextInput::make('label')
                ->required()
                ->string()
                ->maxLength(255),
            Checkbox::make('isRequired')
                ->label('Required'),
            ...$this->fields(),
        ];
    }

    public function getLabel(): string
    {
        return $this->label ?? (string) str(static::type())
            ->afterLast('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();;
    }

    public function getIdentifier(): string
    {
        return static::type();
    }

    abstract public function fields(): array;

    abstract public static function type(): string;

    abstract public static function getInfolistEntry(FormField $field): Entry;

    abstract public static function getFormKitSchema(FormField $field): array;

    public static function getValidationRules(FormField $field): array
    {
        return [];
    }
}
