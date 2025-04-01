<button x-data="{ isLoading: false }" x-on:engage-action-finished-loading.window="isLoading = false"
    x-on:click="isLoading = true; $dispatch('send-sms', { phoneNumberKey: @js($phoneNumber->getKey()) })"
    x-tooltip.raw="Click to send an SMS" type="button" @disabled(!$phoneNumber->can_receive_sms)
    class="flex items-center gap-2">
    @svg('heroicon-m-phone', 'size-5', ['x-show' => '! isLoading'])

    <x-filament::loading-indicator class="size-5" x-show="isLoading" x-cloak />

    {{ $phoneNumber->number }}

    @if (filled($phoneNumber->ext))
        (ext. {{  $phoneNumber->ext }})
    @endif

    @if (filled($phoneNumber->type))
        ({{ $phoneNumber->type }})
    @endif
</button>