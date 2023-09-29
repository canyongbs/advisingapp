<?php

use Filament\Support\Facades\FilamentAsset;
use Assist\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;

?>

<x-filament-panels::page>
    <div wire:init="determineIfConsentWasGiven">
        @if ($consentedToTerms === true && $loading === false)
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
                                <h1 class="mb-1 text-2xl">
                                    {{ $message->from === AIChatMessageFrom::User ? 'You' : 'AI Assistant' }}
                                </h1>
                                <div class="prose dark:prose-invert">{!! str($message->message)->markdown()->sanitizeHtml() !!}</div>
                            </div>
                        </div>
                    @endforeach
                    @if ($showCurrentResponse)
                        <div class="my-4 w-3/4 rounded-lg bg-gray-500 p-4 sm:p-6 lg:px-8" x-data="currentResponseData">
                            <h1 class="mb-1 text-2xl">AI Assistant</h1>
                            <p class="hidden" wire:stream="currentResponse"
                               id="hidden_current_response">{{ $currentResponse }}</p>
                            <p id="current_response" class="prose dark:prose-invert"></p>
                        </div>
                    @endif
                    @if ($renderError)
                        <div class="my-4 w-full rounded-lg bg-gray-200 p-4 sm:p-6 lg:px-8">
                            <h1 class="text-2xl font-bold text-red-400">Something went wrong</h1>
                            <p class="text-black">{{ $error }}</p>
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
                            <x-heroicon-s-bookmark class="h-6 w-6"/>
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
                        <x-heroicon-s-paper-airplane class="h-6 w-6"/>
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
        @elseif($consentedToTerms === false && $loading === false)
            <div class="flex flex-col justify-center">
                <p class="mb-4">
                    You must agree to the terms and conditions before continuing use of this feature.
                </p>
            </div>
        @else
            <div class="flex h-full w-full items-center justify-center">
                <x-filament::loading-indicator class="h-12 w-12"/>
            </div>
        @endif

        @if ($consentedToTerms === false)
            {{-- TODO potentially prevent closure of modal by pressing escape --}}
            <x-filament::modal
                    id="consent-agreement"
                    width="5xl"
                    alignment="center"
                    :close-by-clicking-away="false"
                    :close-button="false"
            >
                @if ($loading === false)
                    <x-slot name="trigger">
                        <x-filament::button>
                            Terms and Conditions
                        </x-filament::button>
                    </x-slot>
                @endif

                <x-slot name="heading">
                    <h1 class="text-center text-xl">
                        {{ $consentAgreement->title }}
                    </h1>
                </x-slot>

                <x-slot name="description">
                    <div class="my-4 border-gray-100 text-center">
                        <p class="prose mx-auto text-gray-100">{{ $consentAgreement->description }}</p>
                    </div>

                    <x-filament::section>
                        <div class="text-center">
                            <p class="prose mx-auto text-gray-100">{{ $consentAgreement->body }}</p>
                        </div>
                    </x-filament::section>
                </x-slot>

                <x-slot name="footer">
                    <form
                            class="flex w-full flex-col"
                            wire:submit="confirmConsent"
                    >
                        <label class="mx-auto">
                            <x-filament::input.checkbox
                                    wire:model="consentedToTerms"
                                    required
                            />
                            <span class="ml-2">
                                I agree to the terms and conditions
                            </span>
                        </label>

                        <div class="mt-4 flex justify-center space-x-4">
                            <x-filament::button
                                    class="mt-4 md:mt-0"
                                    wire:click="denyConsent"
                                    outlined
                                    color="warning"
                            >
                                Cancel
                            </x-filament::button>
                            <x-filament::button
                                    class="mt-4 md:mt-0"
                                    type="submit"
                                    color="success"
                            >
                                I understand
                            </x-filament::button>
                        </div>

                    </form>
                </x-slot>
            </x-filament::modal>
        @endif

    </div>
    <!-- TODO: Need to figure out the best way to bring this in with npm and have it available to the assistantCurrentResponse script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/2.1.0/showdown.min.js"></script>
{{--    <script src="https://cdn.jsdelivr.net/npm/sanitize-html@2.11.0/index.min.js"></script>--}}
    <script src="{{ FilamentAsset::getScriptSrc('assistantCurrentResponse', 'canyon-gbs/assistant') }}"></script>
</x-filament-panels::page>
