<x-filament-widgets::widget>
    <h1 class="font-medium">Welcome, {{ auth()->user()->name }}</h1>

    <p class="text-xs">
        Today is {{ now()->format('l, F j, Y') }} and the current time is {{ now()->format('g:i A') }}.
    </p>
</x-filament-widgets::widget>
