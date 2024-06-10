<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <input
        {{ $getExtraAttributeBag()->merge($getExtraInputAttributes(), escape: false)->merge(
                [
                    'max' => $getMaxValue(),
                    'min' => $getMinValue(),
                    'step' => $getStep(),
                    $applyStateBindingModifiers('wire:model') => $getStatePath(),
                    'type' => 'range',
                ],
                escape: false,
            ) }}
    />
</x-dynamic-component>
