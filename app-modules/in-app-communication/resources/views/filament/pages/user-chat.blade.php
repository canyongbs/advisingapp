<?php

use Filament\Support\Facades\FilamentAsset;
use Assist\InAppCommunication\Models\TwilioConversation;
use Assist\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;
use Illuminate\Support\Facades\Vite;

?>

<x-filament-panels::page
        class="max-h-screen"
        full-height="true"
>
    <div
            class="flex h-full flex-col"
    >
        <div class="grid flex-1 grid-cols-1 gap-6 md:grid-cols-4">
            <div class="col-span-1">
                <div class="flex flex-col gap-y-2">
                    {{ $this->newChatAction }}

                    <ul
                            class="flex flex-col gap-y-1 rounded-xl border border-gray-950/5 bg-white p-2 shadow-sm dark:border-white/10 dark:bg-gray-900">
                        @foreach ($this->conversations as $conversation)
                            @php
                                /** @var TwilioConversation $conversation */
                            @endphp
                            <li @class([
                                        'px-2 group cursor-pointer flex rounded-lg w-full items-center outline-none transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5 space-x-1',
                                        'bg-gray-100 dark:bg-white/5' => $selectedConversation === $conversation['sid'],
                                    ])>
                                <a
                                        @class([
                                            'fi-sidebar-item-button relative flex flex-1 items-center justify-center gap-x-3 rounded-lg py-2 text-sm',
                                        ])
                                        wire:click="selectConversation('{{ $conversation['sid'] }}')"
                                >
                                    <span @class([
                                        'fi-sidebar-item-label flex-1 truncate',
                                        'text-gray-700 dark:text-gray-200' => !$selectedConversation === $conversation['sid'],
                                        'text-primary-600 dark:text-primary-400' => $selectedConversation === $conversation['sid'],
                                    ])>
                                        {{ $conversation->participants()->where('user_id', '!=', auth()->id())->first()->name }}
                                    </span>
                                </a>

                                {{--                                <div>--}}
                                {{--                                    {{ ($this->moveChatAction)(['chat' => $chatItem->id]) }}--}}
                                {{--                                    {{ ($this->editChatAction)(['chat' => $chatItem->id]) }}--}}
                                {{--                                    {{ ($this->deleteChatAction)(['chat' => $chatItem->id]) }}--}}
                                {{--                                </div>--}}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            @if(!is_null($selectedConversation))
                <div
                    x-data="userToUserChat(`{{ $selectedConversation }}`)"
                    wire:key="conversation-{{ $selectedConversation }}"
                    class="col-span-1 flex h-full flex-col gap-2 overflow-hidden md:col-span-3"
                >
                    <div x-show="loading" x-transition.delay.800ms class="self-center flex flex-col items-center">
                        <x-filament::loading-indicator class="h-12 w-12 text-primary-500" />
                        <p class="text-center" x-text="loadingMessage"></p>
                    </div>
                    <template x-if="!loading && error">
                        <div class="self-center flex flex-col items-center">
                            <x-filament::icon class="h-12 w-12 text-primary-500" icon="heroicon-m-exclamation-triangle" />
                            <p class="text-center">Something went wrong...</p>
                            <p class="text-center" x-text="errorMessage"></p>
                            <x-filament::button class="mt-2" x-on:click="errorRetry">Retry</x-filament::button>
                        </div>
                    </template>
                    <div x-show="!loading && !error" x-transition.delay.850ms class="col-span-1 flex h-full flex-col gap-2 overflow-hidden md:col-span-3">
                        <div
                                class="flex max-h-[calc(100vh-24rem)] flex-1 flex-col-reverse overflow-y-scroll rounded-xl border border-gray-950/5 text-sm shadow-sm dark:border-white/10 dark:bg-gray-800"
                        >
                            <div class="divide-y dark:divide-none">
                                <template x-for="message in messages">
                                    <div class="group w-full dark:bg-gray-800">
                                        <div class="m-auto justify-center p-4 text-base md:gap-6 md:py-6">
                                            <div
                                                    class="mx-auto flex flex-1 gap-4 text-base md:max-w-2xl md:gap-6 lg:max-w-[38rem] xl:max-w-3xl">
                                                <div class="relative flex flex-shrink-0 flex-col items-end">
                                                    <div>
                                                        <x-filament::avatar
                                                                class="rounded-full"
                                                                alt="AI Assistant avatar"
                                                                x-bind:src="message.avatar"
                                                        />

                                                    </div>
                                                </div>
                                                <div
                                                        class="relative flex w-[calc(100%-50px)] flex-col gap-1 md:gap-3 lg:w-[calc(100%-115px)]">
                                                    <div class="flex max-w-full flex-grow flex-col gap-3">
                                                        <div
                                                                class="flex min-h-[20px] flex-col items-start gap-3 overflow-x-auto break-words">
                                                            <div x-text="message.message.body"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <form x-bind="submit">
                            <div
                                    class="w-full overflow-hidden rounded-xl border border-gray-950/5 bg-gray-50 shadow-sm dark:border-white/10 dark:bg-gray-700">
                                <div class="bg-white dark:bg-gray-800">
                                    <label
                                            class="sr-only"
                                            for="message_input"
                                    >Type here</label>
                                    <textarea
                                            class="w-full resize-none border-0 bg-white p-4 text-sm text-gray-900 focus:ring-0 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400"
                                            id="message_input"
                                            x-model="message"
                                            rows="4"
                                            placeholder="Type here..."
                                            required
                                    >
                                </textarea>
                                </div>
                                <div class="flex items-center justify-between border-t px-3 py-2 dark:border-gray-600">
                                    <div class="flex items-center gap-3">
                                        <x-filament::button type="submit">
                                            Post
                                        </x-filament::button>

                                        {{--                                <div--}}
                                        {{--                                        class="py-2"--}}
                                        {{--                                        wire:loading--}}
                                        {{--                                >--}}
                                        {{--                                    <x-filament::loading-indicator class="h-5 w-5 text-primary-500" />--}}
                                        {{--                                </div>--}}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="col-span-1 flex h-full flex-col gap-2 overflow-hidden md:col-span-3">
                    <p class="text-xl text-center">Select or create a new Conversation</p>
                </div>
            @endif
        </div>
        <script src="{{ FilamentAsset::getScriptSrc('userToUserChat', 'canyon-gbs/in-app-communication') }}"></script>
    </div>
</x-filament-panels::page>
