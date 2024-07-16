@php
    use Filament\Support\Facades\FilamentView;

    $color = $this->getColor();
    $heading = $this->getHeading();
@endphp
<x-filament-widgets::widget class="fi-wi-chart">
    <x-filament::section class="h-full relative pt-20" :heading="$heading">
        <div class="lg:absolute inset-0 flex items-center justify-center">
            Insufficient Data
        </div>
    </x-filament::section>
</x-filament-widgets::widget>




