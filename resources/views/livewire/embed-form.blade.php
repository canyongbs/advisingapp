<div>
    <x-filament::section>
        <form wire:submit="create">
            {{ $this->form }}

            <x-filament::button type="submit">
                Create
            </x-filament::button>
        </form>

        <x-filament-actions::modals />
    </x-filament::section>
</div>
