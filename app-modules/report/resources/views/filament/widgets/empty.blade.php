@php
    $heading = $this->getHeading();
    $description = $this->getDescription();
@endphp

<x-filament-widgets::widget>
    <x-filament::section
        :description="$description"
        :heading="$heading"
    >
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ $message }}
        </p>
    </x-filament::section>
</x-filament-widgets::widget>
