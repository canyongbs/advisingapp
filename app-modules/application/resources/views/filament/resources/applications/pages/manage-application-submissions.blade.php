<div>
    @include('filament-panels::resources.relation-manager')

    @if ($this->isTableLoaded() && filled($this->defaultTableAction))
        <div
            wire:init="mountAction(@js($this->defaultTableAction), @if (filled($this->defaultTableActionArguments)) @js($this->defaultTableActionArguments) @else {} @endif, @js($this->getDefaultTableActionUrlContext()))"
        ></div>
    @endif
</div>
