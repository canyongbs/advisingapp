<?php

use Filament\Support\Facades\FilamentAsset;
use Assist\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;
use Illuminate\Support\Facades\Vite;

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
                    <div class="rounded-xl bg-white p-2 shadow-sm border border-gray-950/5 dark:bg-gray-900 dark:border-white/10 md:block">
                        <div class="fi-sidebar-group flex flex-col gap-y-2">
                            <x-filament::button
                                icon="heroicon-m-plus"
                                wire:click="newChat"
                            >
                                {{ __('New Chat') }}
                            </x-filament::button>

                            <ul class="fi-sidebar-group-items flex flex-col gap-y-1">
                                @foreach ($chats as $chatItem)
                                    <li @class([
                                        'fi-sidebar-item px-2 group cursor-pointer flex rounded-lg w-full items-center outline-none transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5 space-x-1',
                                        'fi-active fi-sidebar-item-active bg-gray-100 dark:bg-white/5' =>
                                            $chat->id === $chatItem->id,
                                    ])>
                                        <a
                                            @class([
                                                'fi-sidebar-item-button relative flex flex-1 items-center justify-center gap-x-3 rounded-lg py-2 text-sm',
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

                                        <div>
                                            {{ ($this->editChatAction)(['chat' => $chatItem->id]) }}
                                            {{ ($this->deleteChatAction)(['chat' => $chatItem->id]) }}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-span-1 flex h-full gap-2 flex-col overflow-hidden md:col-span-3">
                    <div
                        class="flex rounded-xl shadow-sm border border-gray-950/5 dark:border-white/10 max-h-[calc(100vh-24rem)] flex-1 flex-col-reverse overflow-y-scroll text-sm dark:bg-gray-800">
                        <div class="divide-y dark:divide-none">
                            @foreach ($chat->messages as $message)
                                @switch($message->from)
                                    @case(AIChatMessageFrom::Assistant)
                                        <div
                                            class="group w-full bg-white dark:bg-gray-900">
                                            <div class="m-auto justify-center p-4 text-base md:gap-6 md:py-6">
                                                <div
                                                    class="mx-auto flex flex-1 gap-4 text-base md:max-w-2xl md:gap-6 lg:max-w-[38rem] xl:max-w-3xl">
                                                    <div class="relative flex flex-shrink-0 flex-col items-end">
                                                        <x-filament::avatar
                                                            :src="Vite::asset('resources/images/canyon-ai-headshot.jpg')"
                                                            class="rounded-full"
                                                        />
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
                                                class="group w-full dark:bg-gray-800">
                                                <div class="m-auto justify-center p-4 text-base md:gap-6 md:py-6">
                                                    <div
                                                        class="mx-auto flex flex-1 gap-4 text-base md:max-w-2xl md:gap-6 lg:max-w-[38rem] xl:max-w-3xl">
                                                        <div class="relative flex flex-shrink-0 flex-col items-end">
                                                            <div>
                                                                <x-filament-panels::avatar.user :user="auth()->user()" />
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
                                        class="group w-full bg-white dark:bg-gray-900"
                                        x-data="currentResponseData"
                                    >
                                        <div class="m-auto justify-center p-4 text-base md:gap-6 md:py-6">
                                            <div
                                                class="mx-auto flex flex-1 gap-4 text-base md:max-w-2xl md:gap-6 lg:max-w-[38rem] xl:max-w-3xl">
                                                <div class="relative flex flex-shrink-0 flex-col items-end">
                                                    <div>
                                                        <img
                                                            class="relative flex h-12 w-12 items-center justify-center rounded-sm p-1 text-white"
                                                            src="{{ Vite::asset('resources/images/canyon-ai-headshot.jpg') }}"
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
                                        class="group w-full border-b border-black/10 bg-white dark:border-gray-900/50 dark:bg-gray-900">
                                        <div class="m-auto justify-center p-4 text-base md:gap-6 md:py-6">
                                            <div
                                                class="mx-auto flex flex-1 gap-4 text-base md:max-w-2xl md:gap-6 lg:max-w-[38rem] xl:max-w-3xl">
                                                <div class="relative flex flex-shrink-0 flex-col items-end">
                                                    <div>
                                                        <img
                                                            class="relative flex h-12 w-12 items-center justify-center rounded-sm p-1 text-white"
                                                            src="{{ Vite::asset('resources/images/canyon-ai-headshot.jpg') }}"
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
                            <div class="w-full rounded-xl overflow-hidden shadow-sm border border-gray-950/5 dark:border-white/10 bg-gray-50 dark:bg-gray-700">
                                <div class="bg-white dark:bg-gray-800">
                                    <label
                                        class="sr-only"
                                        for="message_input"
                                    >Type here</label>
                                    <textarea
                                        class="w-full border-0 bg-white resize-none p-4 text-sm text-gray-900 focus:ring-0 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400"
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
                                    <div class="flex items-center gap-3">
                                        @if (! $showCurrentResponse)
                                            <x-filament::button
                                                wire:loading.remove
                                                form="sendMessage,ask"
                                                type="submit"
                                            >
                                                Post
                                            </x-filament::button>
                                        @endif

                                        <div wire:loading class="py-2">
                                            <x-filament::loading-indicator
                                                class="h-5 w-5 text-primary-500"
                                            />
                                        </div>

                                        @error('message')
                                            <p class="ml-auto text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    @if ((! $showCurrentResponse) && (! $chat->id) && $chat->messages->count() > 0)
                                        <div wire:loading.remove class="flex space-x-1 pl-0 sm:pl-2">
                                            {{ $this->saveChatAction }}
                                        </div>
                                    @endif
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

                    <x-slot name="header">
                        <h2 class="text-xl font-semibold text-center text-gray-950 dark:text-white">
                            {{ $consentAgreement->title }}
                        </h2>
                    </x-slot>

                    <div class="prose text-center max-w-none dark:prose-invert">
                        {{ str($consentAgreement->description)->markdown()->sanitizeHtml()->toHtmlString() }}
                    </div>

                    <x-filament::section>
                        <div class="prose text-center max-w-none dark:prose-invert">
                            {{ str($consentAgreement->body)->markdown()->sanitizeHtml()->toHtmlString() }}
                        </div>
                    </x-filament::section>

                    <x-slot name="footer">
                        <form
                            class="flex w-full flex-col gap-6"
                            wire:submit="confirmConsent"
                        >
                            <label class="mx-auto">
                                <x-filament::input.checkbox
                                    wire:model="consentedToTerms"
                                    required="true"
                                />

                                <span class="ml-2 font-medium text-sm">
                                    I agree to the terms and conditions
                                </span>
                            </label>

                            <div class="flex justify-center gap-3">
                                <x-filament::button
                                    wire:click="denyConsent"
                                    outlined
                                    color="warning"
                                >
                                    Cancel
                                </x-filament::button>
                                <x-filament::button
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
