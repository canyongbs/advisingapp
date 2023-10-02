<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Builder\Block as FilamentBlock;
use Filament\Forms\Components\TextInput as FilamentTextInput;

abstract class Block extends FilamentBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->schema([
            FilamentTextInput::make('label')
                ->required()
                ->string()
                ->maxLength(255),
            FilamentTextInput::make('key')
                ->required()
                ->string()
                ->maxLength(255),
            Checkbox::make('required'),
            ...$this->fields(),
        ]);
    }

    abstract public static function display(FormField $field): Field;

    abstract public function fields(): array;
}
