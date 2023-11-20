<div {{ $attributes->class(['grid gap-y-2']) }}>
    <div class="text-sm font-medium leading-6">
        {{-- Deliberately poor formatting to ensure that the asterisk sticks to the final word in the label. --}}
        {{ $label }}@if ($isRequired)
            <sup class="font-medium text-danger-600 dark:text-danger-400">*</sup>
        @endif
    </div>

    {{ $slot }}
</div>
