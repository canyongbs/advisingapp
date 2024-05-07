<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button
            class="mt-4"
            type="submit"
        >
            Submit
        </x-filament::button>
    </form>

    <x-filament-actions::modals />
</x-filament-panels::page>
