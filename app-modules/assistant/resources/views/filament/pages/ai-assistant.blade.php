<x-filament-panels::page>
    <div>
        <div class="h-[50vh] overflow-auto flex flex-col-reverse">
            <div>
                @foreach($chat->messages as $message)
                    <div
                            @class(
                                [
                                    'mx-auto my-4 w-full p-4 sm:p-6 lg:px-8',
                                    'bg-primary-500' => $message->from === 'user',
                                    'bg-gray-500' => $message->from === 'assistant',
                                ]
                            )
                    >
                        <h1 class="text-2xl">{{ $message->from === 'user' ? 'You' : 'AI Assistant' }}</h1>
                        <p>{{ $message->message }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <form
            wire:submit.prevent="saveCurrentMessage"
        >
            <label for="chat" class="sr-only">Your message</label>
            <div class="flex items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700">
                @if(! $chat->id)
                <button wire:loading.attr="disabled" wire:click="save" type="button" class="inline-flex justify-center p-2 text-gray-500 rounded-lg cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                    <x-heroicon-s-bookmark class="w-6 h-6" />
                    <span class="sr-only">Save</span>
                </button>
                @endif
                <div class="block mx-4 p-2.5 w-full">
                    <textarea wire:model.debounce="message" wire:loading.attr="disabled" id="chat" rows="5" class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Your message..."></textarea>
                    <div class="text-red-600">@error('message') {{ $message }} @enderror</div>
                </div>
                <button wire:loading.remove type="submit" class="inline-flex justify-center p-2 text-primary-600 rounded-full cursor-pointer hover:bg-primary-100 dark:text-primary-500 dark:hover:bg-gray-600">
                    <x-heroicon-s-paper-airplane class="w-6 h-6" />
                    <span class="sr-only">Send message</span>
                </button>
                <svg wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </form>
        <script>
            document.addEventListener('livewire:initialized', () => {
                @this.on('current-message-saved', (event) => {
                    @this.dispatch('ask');
                });
            });
        </script>
    </div>
</x-filament-panels::page>
