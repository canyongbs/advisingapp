<div class="flex justify-center items-center pt-16">
    <x-filament::section class="w-1/2">
        <x-slot name="heading">
            {{ $this->embed->name }}
        </x-slot>

        <x-slot name="description">
            {{ $this->embed->description }}
        </x-slot>

        <form wire:submit="create">
            {{ $this->form }}

            <div class="pt-6 gap-3 flex flex-wrap items-center justify-start">
                <x-filament::button type="submit">
                    Create
                </x-filament::button>
            </div>
        </form>

        <x-filament-actions::modals />
    </x-filament::section>
</div>
