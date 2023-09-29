<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormItem;
use Filament\Forms\Components\Textarea as FilamentTextArea;

class TextArea extends Block
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Text Area');
    }

    public static function make(string $name = 'text_area'): static
    {
        return parent::make($name);
    }

    public static function display(FormItem $item): FilamentTextArea
    {
        return FilamentTextArea::make($item->key)
            ->label($item->label)
            ->required($item->required);
    }

    public function fields(): array
    {
        return [];
    }
}
