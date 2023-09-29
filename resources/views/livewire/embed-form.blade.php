<div class="flex justify-center items-center pt-16">
    @if($show)
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

                        <x-filament::button color="danger" wire:click="resetForm">
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
                {{ $this->embed->name }}
            </x-slot>

            <x-slot name="description">
                Thank you for your submission.
            </x-slot>

            <div class="gap-3 flex flex-wrap items-center justify-start">
                <x-filament::button type="button" wire:click="resetForm">
                    Reset
                </x-filament::button>
            </div>
        </x-filament::section>
    @endif
</div>
