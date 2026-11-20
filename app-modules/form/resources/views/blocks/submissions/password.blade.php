<x-form::blocks.field-wrapper class="py-3" :$label :$isRequired :description="$description ?? null">
    @if (filled($response ?? null))
        <div class="fi-not-prose flex flex-wrap items-center gap-3" data-secret-row>
            <span class="text-sm leading-5 text-gray-500" data-secret-mask>Password stored securely</span>
            <code class="hidden rounded bg-gray-100 px-2 py-1 text-sm dark:bg-white/10" data-secret-value></code>
            <span class="hidden text-sm text-danger-600" data-secret-error role="alert"></span>

            <x-filament::link tag="button" type="button" size="sm" data-secret-reveal :data-secret-id="$response">
                Reveal
            </x-filament::link>
        </div>
    @else
        <span class="text-gray-500">No response</span>
    @endif
</x-form::blocks.field-wrapper>
