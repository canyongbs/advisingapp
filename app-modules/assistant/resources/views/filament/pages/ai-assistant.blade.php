<?php

use Filament\Support\Facades\FilamentAsset;
use Assist\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;

?>

<x-filament-panels::page
    class="max-h-screen"
    full-height="true"
>
    <div
        class="flex h-full flex-col"
        wire:init="determineIfConsentWasGiven"
    >
        @if ($consentedToTerms === true && $loading === false)
            <div class="grid flex-1 grid-cols-1 gap-6 md:grid-cols-4">
                <div class="col-span-1">
                    <div
                        class="rounded-xl bg-white p-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 md:block">
                        <li class="fi-sidebar-group flex flex-col gap-y-1">
                            <x-filament::button
                                icon="heroicon-m-plus"
                                wire:click="newChat"
                            >
                                {{ __('New Chat') }}
                            </x-filament::button>
                            <ul class="fi-sidebar-group-items flex flex-col gap-y-1">
                                @foreach ($chats as $chatItem)
                                    <li @class([
                                        'fi-sidebar-item group cursor-pointer flex rounded-lg w-full items-center outline-none transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5 space-x-1',
                                        'fi-active fi-sidebar-item-active bg-gray-100 dark:bg-white/5' => $chat->id === $chatItem->id,
                                    ])>
                                        <a
                                            @class([
                                                'fi-sidebar-item-button relative flex flex-1 items-center justify-center gap-x-3 rounded-lg px-2 py-2 text-sm',
                                            ])
                                            wire:click="selectChat('{{ $chatItem->id }}')"
                                        >
                                            <span @class([
                                                'fi-sidebar-item-label flex-1 truncate',
                                                'text-gray-700 dark:text-gray-200' => !$chat->id === $chatItem->id,
                                                'text-primary-600 dark:text-primary-400' => $chat->id === $chatItem->id,
                                            ])>
                                                {{ $chatItem->name }}
                                            </span>
                                        </a>

                                        {{ ($this->editChatAction)(['chat' => $chatItem->id]) }}
                                        {{ ($this->deleteChatAction)(['chat' => $chatItem->id]) }}
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    </div>
                </div>

                <div class="col-span-1 flex h-full flex-col overflow-hidden md:col-span-3">
                    <div
                        class="flex max-h-[calc(100vh-24rem)] flex-1 flex-col-reverse overflow-y-scroll text-sm dark:bg-gray-800">
                        <div>
                            @foreach ($chat->messages as $message)
                                @switch($message->from)
                                    @case(AIChatMessageFrom::Assistant)
                                        <div
                                            class="text-token-text-primary group w-full border-b border-black/10 bg-gray-50 dark:border-gray-900/50 dark:bg-[#444654]"
                                            data-testid="conversation-turn-3"
                                            style="--avatar-color: #19c37d;"
                                        >
                                            <div class="m-auto justify-center p-4 text-base md:gap-6 md:py-6">
                                                <div
                                                    class="mx-auto flex flex-1 gap-4 text-base md:max-w-2xl md:gap-6 lg:max-w-[38rem] xl:max-w-3xl">
                                                    <div class="relative flex flex-shrink-0 flex-col items-end">
                                                        <div>
                                                            <img
                                                                class="relative flex h-12 w-12 items-center justify-center rounded-sm p-1 text-white"
                                                                src="{{ \Illuminate\Support\Facades\Vite::asset('resources/images/canyon-ai-headshot.jpg') }}"
                                                                alt="AI Assistant avatar"
                                                            >
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="agent-turn relative flex w-[calc(100%-50px)] flex-col gap-1 md:gap-3 lg:w-[calc(100%-115px)]">
                                                        <div class="flex max-w-full flex-grow flex-col gap-3">
                                                            <div
                                                                class="flex min-h-[20px] flex-col items-start gap-3 overflow-x-auto break-words">
                                                                <div class="prose dark:prose-invert">
                                                                    {!! str($message->message)->markdown()->sanitizeHtml() !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex justify-between empty:hidden lg:block">
                                                            {{--                                                            <div --}}
                                                            {{--                                                                class="visible mt-2 flex justify-center gap-2 self-end text-gray-400 md:gap-3 lg:absolute lg:right-0 lg:top-0 lg:mt-0 lg:translate-x-full lg:gap-1 lg:self-center lg:pl-2"> --}}
                                                            {{--                                                                <x-filament::icon --}}
                                                            {{--                                                                    class="ml-auto flex h-6 w-6 cursor-pointer items-center gap-2 rounded-md p-1 text-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 disabled:dark:hover:text-gray-400" --}}
                                                            {{--                                                                    icon="heroicon-o-clipboard" --}}
                                                            {{--                                                                /> --}}
                                                            {{--                                                            </div> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @break

                                    @case(AIChatMessageFrom::User)

                                        @default
                                            <div
                                                class="text-token-text-primary group w-full border-b border-black/10 dark:border-gray-900/50 dark:bg-gray-800">
                                                <div class="m-auto justify-center p-4 text-base md:gap-6 md:py-6">
                                                    <div
                                                        class="mx-auto flex flex-1 gap-4 text-base md:max-w-2xl md:gap-6 lg:max-w-[38rem] xl:max-w-3xl">
                                                        <div class="relative flex flex-shrink-0 flex-col items-end">
                                                            <div>
                                                                <img
                                                                    class="relative flex h-12 w-12 items-center justify-center rounded-sm p-1 text-white"
                                                                    src="{{ \Illuminate\Support\Facades\Vite::asset('resources/images/neutral-profile.jpg') }}"
                                                                    alt="User avatar"
                                                                >
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="relative flex w-[calc(100%-50px)] flex-col gap-1 md:gap-3 lg:w-[calc(100%-115px)]">
                                                            <div class="flex max-w-full flex-grow flex-col gap-3">
                                                                <div
                                                                    class="flex min-h-[20px] flex-col items-start gap-3 overflow-x-auto break-words">
                                                                    <div>{{ $message->message }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @break
                                    @endswitch
                                @endforeach

                                @if ($showCurrentResponse)
                                    <div
                                        class="text-token-text-primary group w-full border-b border-black/10 bg-gray-50 dark:border-gray-900/50 dark:bg-[#444654]"
                                        style="--avatar-color: #19c37d;"
                                        x-data="currentResponseData"
                                    >
                                        <div class="m-auto justify-center p-4 text-base md:gap-6 md:py-6">
                                            <div
                                                class="mx-auto flex flex-1 gap-4 text-base md:max-w-2xl md:gap-6 lg:max-w-[38rem] xl:max-w-3xl">
                                                <div class="relative flex flex-shrink-0 flex-col items-end">
                                                    <div>
                                                        <img
                                                            class="relative flex h-12 w-12 items-center justify-center rounded-sm p-1 text-white"
                                                            src="{{ \Illuminate\Support\Facades\Vite::asset('resources/images/canyon-ai-headshot.jpg') }}"
                                                            alt="Small avatar"
                                                        >
                                                    </div>
                                                </div>
                                                <div
                                                    class="agent-turn relative flex w-[calc(100%-50px)] flex-col gap-1 md:gap-3 lg:w-[calc(100%-115px)]">
                                                    <div class="flex max-w-full flex-grow flex-col gap-3">
                                                        <div
                                                            class="flex min-h-[20px] flex-col items-start gap-3 overflow-x-auto break-words">
                                                            <div
                                                                class="hidden"
                                                                id="hidden_current_response"
                                                                wire:stream="currentResponse"
                                                            >{{ $currentResponse }}</div>
                                                            <div
                                                                class="markdown light prose w-full break-words dark:prose-invert"
                                                                id="current_response"
                                                            ></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($renderError)
                                    <div class="my-4 w-full rounded-lg bg-gray-200 p-4 sm:p-6 lg:px-8">
                                        <h1 class="text-2xl font-bold text-red-400">Something went wrong</h1>
                                        <p class="text-black">{{ $error }}</p>
                                    </div>

                                    <div
                                        class="text-token-text-primary group w-full border-b border-black/10 bg-gray-50 dark:border-gray-900/50 dark:bg-[#444654]"
                                        data-testid="conversation-turn-3"
                                        style="--avatar-color: #19c37d;"
                                    >
                                        <div class="m-auto justify-center p-4 text-base md:gap-6 md:py-6">
                                            <div
                                                class="mx-auto flex flex-1 gap-4 text-base md:max-w-2xl md:gap-6 lg:max-w-[38rem] xl:max-w-3xl">
                                                <div class="relative flex flex-shrink-0 flex-col items-end">
                                                    <div>
                                                        <img
                                                            class="relative flex h-12 w-12 items-center justify-center rounded-sm p-1 text-white"
                                                            src="{{ \Illuminate\Support\Facades\Vite::asset('resources/images/canyon-ai-headshot.jpg') }}"
                                                            alt="AI Assistant avatar"
                                                        >
                                                    </div>
                                                </div>
                                                <div
                                                    class="agent-turn relative flex w-[calc(100%-50px)] flex-col gap-1 md:gap-3 lg:w-[calc(100%-115px)]">
                                                    <div class="flex max-w-full flex-grow flex-col gap-3">
                                                        <div
                                                            class="flex min-h-[20px] flex-col items-start gap-3 overflow-x-auto break-words">
                                                            <h1 class="text-2xl font-bold text-red-400">Something went wrong
                                                            </h1>
                                                            <p class="text-black">{{ $error }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <form wire:submit.prevent="sendMessage">
                            <div
                                class="w-full rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-600 dark:bg-gray-700">
                                <div class="rounded-t-lg bg-white px-4 py-2 dark:bg-gray-800">
                                    <label
                                        class="sr-only"
                                        for="message_input"
                                    >Type here</label>
                                    <textarea
                                        class="w-full border-0 bg-white px-0 text-sm text-gray-900 focus:ring-0 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400"
                                        id="message_input"
                                        rows="4"
                                        placeholder="Type here..."
                                        required
                                        wire:model.debounce="message"
                                        wire:loading.attr="disabled"
                                        {{-- TODO: For some reason this causes issues with the response streaming --}}
                                        {{-- @keydown.cmd.enter='$wire.sendMessage' --}}
                                    >
                                    </textarea>
                                </div>
                                <div class="flex items-center justify-between border-t px-3 py-2 dark:border-gray-600">
                                    <div class="flex items-center space-x-2">
                                        <button
                                            class="inline-flex items-center rounded-lg bg-primary-500 px-4 py-2.5 text-center text-xs font-medium text-white hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 dark:focus:ring-primary-900"
                                            type="submit"
                                            wire:loading.remove
                                            x-on:click="$wire.showCurrentResponse = true"
                                        >
                                            Post
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
                                        @error('message')
                                            <p class="ml-auto text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="flex space-x-1 pl-0 sm:pl-2">
                                        @if (!$chat->id && $chat->messages->count() > 0)
                                            {{ $this->saveChatAction }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @elseif($consentedToTerms === false && $loading === false)
                <div class="flex flex-col justify-center">
                    <p class="mb-4">
                        You must agree to the terms and conditions before continuing use of this feature.
                    </p>
                </div>
            @else
                <div class="flex h-full w-full items-center justify-center">
                    <x-filament::loading-indicator class="h-12 w-12" />
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
                                    required="true"
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
        <script src="{{ FilamentAsset::getScriptSrc('assistantCurrentResponse', 'canyon-gbs/assistant') }}"></script>
    </div>
</x-filament-panels::page>
