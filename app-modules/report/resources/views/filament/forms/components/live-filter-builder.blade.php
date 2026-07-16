@php
    use AdvisingApp\Report\Filament\Forms\Components\LiveFilterBuilder\LiveFilterBuilderComponent;

    $id = $getId();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        {{
            $attributes
                ->merge(['id' => $id], escape: false)
                ->merge($getExtraAttributes(), escape: false)
        }}
    >
        @livewire(LiveFilterBuilderComponent::class, [
            'groupModel' => $getGroupModel()->value,
            $applyStateBindingModifiers('wire:model') => $getStatePath(),
        ], key($getLivewireKey()))
    </div>
</x-dynamic-component>
