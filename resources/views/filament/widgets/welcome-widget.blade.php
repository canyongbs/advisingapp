<x-filament-widgets::widget>
    <h1 class="font-medium">Welcome, {{ auth()->user()->name }}</h1>

    <p class="text-xs">
        Today is {{ now(auth()->user()->timezone)->format('l, F j, Y') }} and the current time is
        {{ now(auth()->user()->timezone)->format('g:i A') }}.
    </p>
</x-filament-widgets::widget>
