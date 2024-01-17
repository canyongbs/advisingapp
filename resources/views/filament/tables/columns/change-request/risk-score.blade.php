@php
    
    function getClassesFromRisk($value)
    {
        $classMap = [
            '1-4' => 'bg-green-400/10 border-green-500 text-green-500',
            '5-10' => 'bg-yellow-400/10 border-yellow-500 text-yellow-500',
            '11-16' => 'bg-orange-400/10 border-orange-500 text-orange-500',
            '17-25' => 'bg-red-400/10 border-red-600 text-red-600',
        ];
    
        foreach ($classMap as $range => $classes) {
            [$min, $max] = explode('-', $range);
            if ($value >= (int) $min && $value <= (int) $max) {
                return $classes;
            }
        }
    }
    
@endphp

<div>
    <x-filament::badge class="{{ getClassesFromRisk($getState()) }}">
        {{ $getState() }}
    </x-filament::badge>
</div>
