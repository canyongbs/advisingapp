<?php

namespace Assist\Form\Filament\Blocks;

class TextInput
{
    private function __construct(private string $label, private string $key) {}

    public static function make(string $label, string $key): static
    {
        return app(static::class, ['label' => $label, 'key' => $key]);
    }
}
