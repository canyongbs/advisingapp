@php
    use function Filament\Support\prepare_inherited_attributes;
@endphp
<div class="flex items-center gap-2">
    <x-filament::button
        :attributes="prepare_inherited_attributes($attributes)"
        :icon="$action->getIcon()"
        :color="$action->getColor()"
        :size="$action->getSize()"
        wire:click="mountAction('{{ $action->getName() }}')"
    >
        {{ $action->getLabel() ?? $action->getName() }}
    </x-filament::button>
    @php
        $tooltipText = "When you subscribe to a {$type}, you will receive updates via notifications on that {$type}'s activities. You may subscribe or unsubscribe at any time.";
    @endphp
    <div
        class="cursor-help"
        x-data
        x-tooltip.raw="{{ $tooltipText }}"
    >
        <x-heroicon-o-question-mark-circle class="h-5 w-5" />
    </div>
</div>
