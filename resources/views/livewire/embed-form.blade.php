<div class="flex items-center justify-center pt-16">
    @if ($show)
        <x-filament::section class="w-1/2">
            <x-slot name="heading">
                {{ $this->form->name }}
            </x-slot>

            <x-slot name="description">
                {{ $this->form->description }}
            </x-slot>

            <form wire:submit="create">
                {{ $this->getForm('form') }}

                <div class="flex flex-wrap items-center justify-start gap-3 pt-6">
                    <x-filament::button type="submit">
                        Submit
                    </x-filament::button>
                    <x-filament::modal id="reset">
                        <x-slot name="trigger">
                            <x-filament::button color="danger">
                                Reset
                            </x-filament::button>
                        </x-slot>

                        <x-slot name="heading">
                            Are you sure?
                        </x-slot>

                        <x-filament::button
                            color="danger"
                            wire:click="resetForm"
                        >
                            Reset
                        </x-filament::button>
                    </x-filament::modal>
                </div>
            </form>

            <x-filament-actions::modals />
        </x-filament::section>
    @else
        <x-filament::section class="w-1/2">
            <x-slot name="heading">
                {{ $this->form->name }}
            </x-slot>

            <x-slot name="description">
                Thank you for your submission.
            </x-slot>

            <div class="flex flex-wrap items-center justify-start gap-3">
                <x-filament::button
                    type="button"
                    wire:click="resetForm"
                >
                    Reset
                </x-filament::button>
            </div>
        </x-filament::section>
    @endif
</div>
