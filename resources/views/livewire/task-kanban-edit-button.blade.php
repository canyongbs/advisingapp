<div>
    {{ $this->editAction }}

    @once
        @teleport('body')
        <x-filament-actions::modals />
        @endteleport
    @endonce
</div>
