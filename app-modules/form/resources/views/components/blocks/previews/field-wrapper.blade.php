<div class="grid gap-y-2">
    <div class="text-sm font-medium leading-6">
        {{-- Deliberately poor formatting to ensure that the asterisk sticks to the final word in the label. --}}
        {{ $label }}@if ($required)<sup class="text-danger-600 dark:text-danger-400 font-medium">*</sup>
        @endif
    </div>

    {{ $slot }}
</div>
