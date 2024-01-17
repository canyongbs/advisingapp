@php
    
    function getClassesFromRisk($value)
    {
        $classMap = [
            '1-4' => 'border-green-500 bg-green-400/10 text-green-500 ring-green-500 dark:border-green-500 dark:bg-green-400/10 dark:text-green-500 dark:ring-green-500',
            '5-10' => 'border-yellow-500 bg-yellow-400/10 text-yellow-500 ring-yellow-500 dark:border-yellow-500 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-500',
            '11-16' => 'border-orange-500 bg-orange-400/10 text-orange-500 ring-orange-500 dark:border-orange-500 dark:bg-orange-400/10 dark:text-orange-500 dark:ring-orange-500',
            '17-25' => 'border-red-600 bg-red-400/10 text-red-600 ring-red-600 dark:border-red-600 dark:bg-red-400/10 dark:text-red-600 dark:ring-red-600',
        ];
    
        foreach ($classMap as $range => $classes) {
            [$min, $max] = explode('-', $range);
            if ($value >= (int) $min && $value <= (int) $max) {
                return $classes;
            }
        }
    }
    
@endphp

<div class="fi-ta-text grid w-full gap-y-1">
    <x-filament::badge class="{{ getClassesFromRisk($getState()) }} w-1/2">
        {{ $getState() }}
    </x-filament::badge>
</div>
