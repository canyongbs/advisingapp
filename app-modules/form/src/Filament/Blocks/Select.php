<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormItem;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select as FilamentSelect;

class Select extends Block
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Select');
    }

    public static function make(string $name = 'select'): static
    {
        return parent::make($name);
    }

    public static function display(FormItem $item): FilamentSelect
    {
        return FilamentSelect::make($item->key)
            ->label($item->label)
            ->required($item->required)
            ->options($item->content['options']);
    }

    public function fields(): array
    {
        return [
            KeyValue::make('options'),
        ];
    }
}
