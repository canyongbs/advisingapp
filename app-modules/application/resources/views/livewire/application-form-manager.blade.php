<div>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6">
            {{ $this->formActions }}
        </div>
    </form>
    <x-filament-actions::modals />
</div>
