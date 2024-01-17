@php
    use AdvisingApp\ServiceManagement\Models\ChangeRequest;
    
    $classes = match (ChangeRequest::getColorBasedOnRisk($getState())) {
        'green' => 'border-green-500 bg-green-400/10 text-green-500 ring-green-500 dark:border-green-500 dark:bg-green-400/10 dark:text-green-500 dark:ring-green-500',
        'yellow' => 'border-yellow-500 bg-yellow-400/10 text-yellow-500 ring-yellow-500 dark:border-yellow-500 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-500',
        'orange' => 'border-orange-500 bg-orange-400/10 text-orange-500 ring-orange-500 dark:border-orange-500 dark:bg-orange-400/10 dark:text-orange-500 dark:ring-orange-500',
        'red' => 'border-red-600 bg-red-400/10 text-red-600 ring-red-600 dark:border-red-600 dark:bg-red-400/10 dark:text-red-600 dark:ring-red-600',
        default => '',
    };
@endphp

<div class="fi-ta-text grid w-full gap-y-1">
    <x-filament::badge class="{{ $classes }} w-1/2">
        {{ $getState() }}
    </x-filament::badge>
</div>
