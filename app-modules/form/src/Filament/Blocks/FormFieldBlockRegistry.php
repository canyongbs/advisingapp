<?php

namespace Assist\Form\Filament\Blocks;

class FormFieldBlockRegistry
{
    public static function get(): array
    {
        return [
            TextInputFormFieldBlock::class,
            TextAreaFormFieldBlock::class,
            SelectFormFieldBlock::class,
            SignatureFormFieldBlock::class,
        ];
    }

    public static function getInstances(): array
    {
        return collect(static::get())
            ->map(fn (string $block): FormFieldBlock => $block::make())
            ->all();
    }

    public static function keyByType(): array
    {
        return collect(static::get())
            ->mapWithKeys(fn (string $block): array => [$block::type() => $block])
            ->all();
    }
}
