<div class="px-4 space-y-3">
    <p class="text-xs">These codes can be used to recover access to your account if your device is lost. Warning! These codes will only be shown once.</p>
    <div>
    @foreach ($codes->toArray() as $code )
        <span class="inline-flex items-center p-1 text-xs font-medium text-gray-800 dark:text-gray-400 bg-gray-100 rounded-full dark:bg-gray-900">{{ $code }}</span>
    @endforeach
    </div>
    <div class="inline-block text-xs">
        {{-- <span
            x-data="{}"
            :x-on:click.prevent="window.navigator.clipboard.writeText(@js($codes->join(',')));$tooltip('Copied!');"
            class="flex items-center cursor-pointer"
        >
            @svg('heroicon-s-clipboard-document', 'w-4 mr-2')
            <span class="">Copy to clipboard</span>
        </span> --}}
        <div
            class="visible mt-2 flex justify-center gap-2 self-end text-gray-400 md:gap-3"
            x-data="{
                messageCopied: false,
                copyMessage: function() {
                    navigator.clipboard.writeText(@js($codes->join(',')))
            
                    this.messageCopied = true
            
                    setTimeout(() => { this.messageCopied = false }, 2000)
                }
            }"
        >
            <span
                x-on:click="copyMessage"
                class="flex items-center cursor-pointer text-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 disabled:dark:hover:text-gray-400 p-1 rounded-md"
            >
                <x-filament::icon
                    class="ml-auto flex h-6 w-6 cursor-pointer items-center gap-2"
                    icon="heroicon-o-clipboard-document-check"
                    x-show="messageCopied"
                />
                <x-filament::icon
                    class="ml-auto flex h-6 w-6 cursor-pointer items-center gap-2"
                    icon="heroicon-o-clipboard"
                    x-show="! messageCopied"
                />
                <span class="">Copy to clipboard</span>
            </span>
        </div>
    </div>
</div>