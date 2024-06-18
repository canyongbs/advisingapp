<div class="px-4 space-y-3">
    <p class="text-xs">These codes can be used to recover access to your account if your device is lost. Warning! These codes will only be shown once.</p>
    <div>
    @foreach ($codes->toArray() as $code )
        <span class="inline-flex items-center p-1 text-xs font-medium text-gray-800 dark:text-gray-400 bg-gray-100 rounded-full dark:bg-gray-900">{{ $code }}</span>
    @endforeach
    </div>
    <div class="inline-block text-xs">
        <a
            x-data="{}"
            :x-on:click.prevent="window.navigator.clipboard.writeText(@js($codes->join(',')));$tooltip('Copied!');"
            href="#"
            class="flex items-center"
        >
            @svg('heroicon-s-clipboard-document', 'w-4 mr-2')
            <span class="">Copy to clipboard</span>
        </a>
    </div>
</div>