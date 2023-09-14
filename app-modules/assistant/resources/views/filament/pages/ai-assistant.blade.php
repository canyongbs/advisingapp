<?php
use Assist\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;
?>

<x-filament-panels::page>
    <div>
        <div class="flex h-[50vh] flex-col-reverse overflow-auto">
            <div>
                @foreach ($chat->messages as $message)
                    <div @class([
                        'flex my-4 w-full',
                        'justify-end' => $message->from === AIChatMessageFrom::User,
                        'justify-start' => $message->from === AIChatMessageFrom::Assistant,
                    ])>
                        <div @class([
                            'w-3/4 p-4 sm:p-6 lg:px-8 rounded-lg',
                            'bg-primary-500' => $message->from === AIChatMessageFrom::User,
                            'bg-gray-500' => $message->from === AIChatMessageFrom::Assistant,
                        ])>
                            <h1 class="text-2xl">
                                {{ $message->from === AIChatMessageFrom::User ? 'You' : 'AI Assistant' }}
                            </h1>
                            <p>{{ $message->message }}</p>
                        </div>
                    </div>
                @endforeach
                @if ($showCurrentResponse)
                    <div clas="w-3/4 p-4 sm:p-6 lg:px-8 rounded-lg my-4 bg-gray-500">
                        <h1 class="text-2xl">AI Assistant</h1>
                        <p wire:stream="currentResponse">{{ $currentResponse }}</p>
                    </div>
                @endif
            </div>
        </div>

        <form wire:submit.prevent="sendMessage">
            <label
                class="sr-only"
                for="chat"
            >Your message</label>
            <div class="flex items-center rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-700">
                @if (!$chat->id)
                    <button
                        class="inline-flex cursor-pointer justify-center rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                        type="button"
                        wire:loading.attr="disabled"
                        wire:click="save"
                    >
                        <x-heroicon-s-bookmark class="h-6 w-6" />
                        <span class="sr-only">Save</span>
                    </button>
                @endif
                <div class="mx-4 block w-full p-2.5">
                    <textarea
                        class="mx-4 block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                        id="chat"
                        wire:model.debounce="message"
                        wire:loading.attr="disabled"
                        rows="5"
                        placeholder="Your message..."
                    ></textarea>
                    <div class="text-red-600">
                        @error('message')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <button
                    class="inline-flex cursor-pointer justify-center rounded-full p-2 text-primary-600 hover:bg-primary-100 dark:text-primary-500 dark:hover:bg-gray-600"
                    type="submit"
                    wire:loading.remove
                    x-on:click="$wire.showCurrentResponse = true"
                >
                    <x-heroicon-s-paper-airplane class="h-6 w-6" />
                    <span class="sr-only">Send message</span>
                </button>
                <svg
                    class="-ml-1 mr-3 h-5 w-5 animate-spin text-primary-600"
                    wire:loading
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    ></circle>
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                </svg>
            </div>
        </form>
    </div>
</x-filament-panels::page>
