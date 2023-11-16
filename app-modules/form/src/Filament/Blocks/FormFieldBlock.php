<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use FilamentTiptapEditor\TiptapBlock;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput as FilamentTextInput;

abstract class FormFieldBlock extends TiptapBlock
{
    public string $preview = 'form::blocks.previews.default';

    public string $rendered = 'form::blocks.submissions.default';

    public ?string $icon = 'heroicon-m-cube';

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
            ->ucfirst();
    }

    public function getIdentifier(): string
    {
        return static::type();
    }

    public function fields(): array
    {
        return [];
    }

    abstract public static function type(): string;

    abstract public static function getFormKitSchema(FormField $field): array;

    public static function getValidationRules(FormField $field): array
    {
        return [];
    }

    public static function getSubmissionState(mixed $response): array
    {
        return [
            'response' => $response,
        ];
    }
}
