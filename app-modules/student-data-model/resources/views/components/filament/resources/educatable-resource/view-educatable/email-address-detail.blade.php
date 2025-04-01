<button x-data="{ isLoading: false }" x-on:engage-action-finished-loading.window="isLoading = false"
    x-on:click="isLoading = true; $dispatch('send-email', { emailAddressKey: @js($emailAddress->getKey()) })"
    x-tooltip.raw="Click to send an email" type="button" class="flex items-center gap-2">
    @svg('heroicon-m-envelope', 'size-5', ['x-show' => '! isLoading'])

    <x-filament::loading-indicator class="size-5" x-show="isLoading" x-cloak />

    {{ $emailAddress->address }}

    @if (filled($emailAddress->type))
        ({{ $emailAddress->type }})
    @endif
</button>